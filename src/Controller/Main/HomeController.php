<?php

namespace App\Controller\Main;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ACSEO\TypesenseBundle\Finder\TypesenseQuery;

class HomeController extends AbstractController
{
    private PostRepository $postRepository;
    private PaginatorInterface $paginator;
    private $postFinder;
    private CommentRepository $commentRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        $postFinder,
        CommentRepository $commentRepository,
        CategoryRepository $categoryRepository
    )
    {
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
        $this->postFinder = $postFinder;
        $this->commentRepository = $commentRepository;
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $posts = $this->paginator->paginate(
            $this->postRepository->findAllByDesc(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('main/home/index.html.twig', [
            'controller_name' => 'Derniers articles',
            'posts' => $posts
        ]);
    }

    #[Route('/search', name: 'app_search')]
    public function search(): Response
    {
        $wordSearch = 'test';
        $query = new TypesenseQuery($wordSearch, 'title');
        $results = $this->postFinder->rawQuery($query)->getResults();

        return $this->render('main/home/search.html.twig', [
            'results' => $results
        ]);
    }

    #[Route('/article/{slug}', name: 'app_post')]
    public function show(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            // comment response
            $parentId = $form->get('parent_id')->getData();
            $comment->setParent($this->commentRepository->find($parentId));

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('app_post',['slug' => $post->getSlug()]);
        }

        return $this->renderForm('main/home/post.html.twig', [
            'post' => $post,
            'categories' => $this->categoryRepository->findAll(),
            'commentForm' => $form
        ]);
    }
}
