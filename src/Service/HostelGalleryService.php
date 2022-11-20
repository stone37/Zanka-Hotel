<?php

namespace App\Service;

use App\Entity\Hostel;
use App\Entity\HostelGallery;
use App\Manager\OrphanageManager;
use App\Repository\HostelGalleryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RequestStack;

class HostelGalleryService
{
    private OrphanageManager $orphanageManager;
    private UploadService $uploadService;
    private RequestStack $request;
    private HostelGalleryRepository $repository;

    public function __construct(
        OrphanageManager $orphanageManager,
        UploadService $uploadService,
        HostelGalleryRepository $repository,
        RequestStack $request
    )
    {
        $this->orphanageManager = $orphanageManager;
        $this->uploadService = $uploadService;
        $this->request = $request;
        $this->repository = $repository;
    }

    public function add(Hostel $hostel)
    {
        $files = $this->uploadService->getFilesUpload($this->request->getSession());

        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            $gallery = (new HostelGallery())
                ->setFile(new File($file->getPathname()));

            $this->repository->add($gallery, false);

            $hostel->addGallery($gallery);
        }

        $this->repository->flush();

        return true;
    }

    public function initialize(Request $request)
    {
        if (!$request->isMethod('POST')) {
            $request->getSession()->set('app_gallery_image', []);
            $this->orphanageManager->initClear($request->getSession());
        }
    }
}

