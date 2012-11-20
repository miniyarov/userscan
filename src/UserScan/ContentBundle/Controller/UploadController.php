<?php

namespace UserScan\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
    public function handleAction(Request $request, $project_id, $tester_id)
    {
        /** @var $uploadedFile \Symfony\Component\HttpFoundation\File\UploadedFile */
        $uploadedFile = $request->files->get('video');
        if (null === $uploadedFile) {
            return new Response('File upload failed. Error: No data', 400);
        }

        $targetDir = $this->container->getParameter('video_path');

        if ('mp4' == $uploadedFile->getExtension()) {
            return new Response('File upload failed. Error: Wrong Extension', 400);
        }

        $uploadedFile->move($targetDir, sprintf('%s_%s.mp4', $tester_id, $project_id));

        return new Response('File upload successfull');
    }
}