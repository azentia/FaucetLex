<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/faucet", name="faucet_")
 */
class FaucetController extends Controller
{

    public const CLAIM_INTERVAL = '30M';
    public const CLAIM_REWARD = 1;

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('faucet/index.html.twig');
    }

    /**
     * @Route("/claim", name="claim")
     */
    public function claim(UserInterface $user)
    {
        $now = new \Datetime("now");
        $lastClaim = $user->getLastClaim();

        if ($now > $lastClaim->add(new \DateInterval('PT'. self::CLAIM_INTERVAL .''))) {
            $user->increaseSatochi(self::CLAIM_REWARD);
            $user->setLastClaim();

            $this->getDoctrine()->getManager()->flush();
        }

        return new JsonResponse(["satochi" => $user->getSatochi()]);
    }
}