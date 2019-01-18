<?php

namespace App\Controller\Blog;

use http\Env\Request;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Elasticsearch\ClientBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends Controller
{
    /**
     * @Route("/show/{id}"),
     * methods={"GET"},
     * name="app_blog_blog_show",
     * requirements={"id"="\d+"}
     */
    public function show($id)
    {
        $em = $this->getDoctrine()->getManager();

        $blog = $em->getRepository('App\Entity\Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $comments = $em->getRepository('App\Entity\Comment')
            ->getCommentsForBlog($blog->getId());

        return $this->render('blog/show.html.twig', array(
            'blog'      => $blog,
            'comments' => $comments
        ));
    }

    /**
     * @Route("/article/find"),
     * methods={"GET"},
     * name="app_blog_blog_find",
     */
    public function find(HttpRequest $request)
    {
        if ($request->isXmlHttpRequest() && $request->getMethod() == 'POST') {
            try {
                $client = ClientBuilder::create()
                    ->setHosts(['elasticsearch'])
                    ->build();

                $params = [
                    'index' => 'blog',
                    'type' => 'article',
                    'body' => [
                        'query' => [
                            'match' => [
                                'text' => $request->get('query')
                            ]
                        ]
                    ]
                ];

                $response = $client->search($params);
                return new JsonResponse($response);

            } catch(\Exception $e) {
                return new JsonResponse($e->getMessage());
            }
        }
    }
}