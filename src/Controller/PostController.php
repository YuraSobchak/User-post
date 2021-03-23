<?php


namespace App\Controller;

use App\Form\PostType;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{
  /**
   * @Route("/posts", name="posts")
   * @param PaginatorInterface $paginator
   * @param Request $request
   * @return Response
   */
  public function index(PaginatorInterface $paginator, Request $request)
  {
    if ($request->get('sort') === 'author') {
      $posts = $this->getDoctrine()->getRepository(Post::class)->sortByAuthors();
    } elseif ($request->get('sort') === 'date') {
      $posts = $this->getDoctrine()->getRepository(Post::class)->findBy([], ['date' => 'DESC']);
    } else {
      $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
    }
    $pagination = $paginator->paginate(
      $posts,
      $request->query->getInt('page', 1),
      5
    );

    return $this->render('posts/index.html.twig', [
      'posts' => $pagination,
      'total' => count($posts),
    ]);
  }

  /**
   * @Route("/posts/create", name="createPost")
   * @param Request $request
   * @param FileUploader $fileUploader
   * @return Response
   * @throws \Exception
   */
  public function newPost(Request $request, FileUploader $fileUploader) {
    $user = $this->getUser();

    $form = $this->createForm(PostType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $post = new Post();
      $post->setAuthor($user);
      $post->setDate(new \DateTime());
      if ($form->get('image')->getData()) {
        $image = $fileUploader->upload($form->get('image')->getData());
        $post->setImage($image);
      }
      $post->setTitle($form->get('title')->getData());
      $post->setDescription($form->get('description')->getData());

      $em = $this->getDoctrine()->getManager();
      $em->persist($post);
      $em->flush();

      return $this->redirectToRoute('posts');
    }

    return $this->render('posts/create.html.twig', [
      'form' => $form->createView(),
    ]);
  }

  /**
   * @Route("/posts/{id}", name="post")
   * @param $id
   * @return Response
   */
  public function post($id) {
    $post = $this
      ->getDoctrine()
      ->getRepository(Post::class)
      ->find($id);

    return $this->render('posts/post.html.twig', [
      'post' => $post,
    ]);
  }
}