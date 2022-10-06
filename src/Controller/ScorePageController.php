<?php

namespace App\Controller;

use App\Service\PdfService;
use Dompdf\Dompdf;
use Dompdf\Options;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScorePageController extends AbstractController
{
    #[Route('/score/won/{won}', name: 'app_score_page')]
    public function index($won, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $session = $request->getSession();
        $tab = $session->get('tableWordle');
        $data = $session->get('wordToGuess');

        return $this->render('score_page/index.html.twig', [
            'controller_name' => 'ScorePageController',
            'won' => $won,
            'tab' => $tab,
            'wordToGuess' => $data["word"]
        ]);
    }

    #[Route('/score/won/{won}/screenshot', name: 'app_screenshot_page')]
    public function screenshot($won, \Symfony\Component\HttpFoundation\Request $request, TestController $testController = null): Response
    {
        $session = $request->getSession();
        $tab = $session->get('tableWordle');
        $data = $session->get('wordToGuess');

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $dompdf->set_base_path("css");
        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed'=> TRUE
            ]
        ]);
        $dompdf->setHttpContext($contxt);

        $html = $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));


        return $this->render('score_page/index.html.twig', [
            'controller_name' => 'ScorePageController',
            'won' => $won,
            'tab' => $tab,
            'wordToGuess' => $data["word"]
        ]);
    }
}
