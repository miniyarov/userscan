<?php

namespace UserScan\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
    public function handleAction(Request $request, $project_id, $tester_id)
    {
        $uploadedFile = $request->files->get('video');
        if (null === $uploadedFile) {
            return new Response('File upload failed', 400);
        }

        $targetDir = $this->container->getParameter('video_path');


        $uploadedFile->move($targetDir, $tester_id . '.wrong');

        return new Response('File upload successfull');
    }
}