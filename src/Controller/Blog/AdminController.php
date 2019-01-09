<?php

namespace App\Controller\Blog;

use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Blog;
use App\Form\Blog\ArticleType;
use Symfony\Component\Filesystem\Filesystem;
use App\Service\UploadService;

class AdminController extends Controller
{
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("/admin/index"),
     * methods={"GET"},
     * name="app_blog_admin_index",
     */
    public function index()
    {
        return $this->render('blog/admin/index.html.twig');
    }

    public function sidebar()
    {
        return $this->render('blog/admin/sidebar.html.twig');
    }

    /**
     * @Route("/admin/add"),
     * methods={"GET"},
     * name="app_blog_admin_add",
     */
    public function add()
    {
        $blog = new Blog();
        $form = $this->createForm(ArticleType::class, $blog);

        return $this->render('blog/admin/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/delete"),
     * methods={"GET|POST"},
     * name="app_blog_admin_delete",
     */
    public function delete(Request $request)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $blogs = $em->getRepository('App\Entity\Blog')
            ->getTitles();

        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {
            try {
                $article = $em->getRepository(Blog::class)->find($request->get('id'));
                $this->filesystem->remove($this->get('kernel')->getProjectDir() .
                    UploadService::ARTICLE_DIR .
                    $article->getImage());

                $em->remove($article);
                $em->flush();
                return new JsonResponse(['status' => 200]);
            } catch(\Exception $e) {
                return new JsonResponse(['status' => 400]);
            }
        }

        return $this->render('blog/admin/delete.html.twig', array(
            'blogs' => $blogs
        ));
    }

    /**
     * @Route("/save_article"),
     * methods="POST",
     * name="app_blog_admin_save"
     */
    public function save(Request $request)
    {
        $blog = new Blog();
        $em = $this->getDoctrine()->getManager();
        $uploadService = new UploadService($webPath = $this->get('kernel')->getProjectDir() . UploadService::ARTICLE_DIR);
        $form = $this->createForm(ArticleType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $fileName = $uploadService->loadArticlePhoto($request->files->get(ArticleType::BLOCK_PREFIX)['file']);
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('register-error-notice', $e->getMessage());

                return $this->redirectToRoute('app_blog_admin_add');
            }

            $blog->setImage($fileName);
            $em->persist($blog);
            $em->flush();

            $this->get('session')->getFlashBag()->add('register-success-notice', 'You have successfully add article');

            return $this->redirectToRoute('app_blog_admin_add');
        }

        $this->get('session')->getFlashBag()->add('register-error-notice', 'Adding failed');

        return $this->redirectToRoute('app_blog_admin_add');
    }
}
