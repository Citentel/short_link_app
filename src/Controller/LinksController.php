<?php


namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LinksController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route("/")
     */
    public function homePage(Request $request): Response
    {
        return $this->render('pages/homePage.html.twig',[
            'host' => $request->server->get('SYMFONY_PROJECT_DEFAULT_ROUTE_URL')
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/add")
     * @throws \Exception
     */
    public function createShortLink(Request $request): JsonResponse
    {
        if (empty($request->getContent())) {
            return $this->json([
                'code' => 409,
                'message' => 'request can not be empty'
            ]);
        }

        try {
            $json = json_decode($request->getContent(), true);
        } catch (\Exception $e) {
            return $this->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }

        if (!isset($json['url'])) {
            return $this->json([
                'code' => 404,
                'message' => 'request must contain url'
            ]);
        }

        /** @var Link $isExistShortLink */
        $isExistShortLink = $this->getDoctrine()->getRepository(Link::class)->findOneBySrc($json['url']);

        if (!empty($isExistShortLink)) {
            return $this->json([
                'code' => 200,
                'message' => 'link exist',
                'data' => $request->server->get('SYMFONY_PROJECT_DEFAULT_ROUTE_URL') . 'get/' . $isExistShortLink->getName(),
            ]);
        }

        do {
            $randomSting = bin2hex(random_bytes(6));
            $isNameExist = $this->getDoctrine()->getRepository(Link::class)->findOneByName($randomSting);
        } while (!empty($isNameExist));

        $shortLink = (new Link())
            ->setSrc($json['url'])
            ->setName($randomSting);

        $em = $this->getDoctrine()->getManager();

        $em->persist($shortLink);
        $em->flush();

        return $this->json([
            'code' => 200,
            'message' => 'link added',
            'data' => $request->server->get('SYMFONY_PROJECT_DEFAULT_ROUTE_URL') . 'get/' . $shortLink->getName(),
        ]);
    }

    /**
     * @param string $name
     * @return RedirectResponse
     * @Route("/get/{name}")
     */
    public function getOriginLink(string $name): RedirectResponse
    {
        /** @var Link $isNameExist */
        $isNameExist = $this->getDoctrine()->getRepository(Link::class)->findOneByName($name);

        if ($isNameExist === null) {
            return $this->redirect('/');
        }

        return $this->redirect($isNameExist->getSrc());
    }
}