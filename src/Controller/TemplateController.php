<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\Template;
use WebWMS\Entity\TemplateType;
use WebWMS\Service\CSRFProtectionService;
use WebWMS\Service\FileUploader;
use WebWMS\Service\MailService;
use WebWMS\Service\TemplatesService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        TemplatesServiceController
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
#[Route(path: '/settings/templates')]
class TemplateController extends AbstractController
{
    public function __construct(
        private Requirements $requirements
    ) {
    }

    /**
     * Index-View.
     */
    #[Route('/', name: 'settings.templates.overview', methods: ['GET'])]
    public function indexAction(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $templates = $entityManager->getRepository(Template::class)->findAll();

        return $this->render('Templates/index.html.twig', [
            'templates' => $templates,
            'appName' => $this->requirements->getAppName(),
            'appVersion' => $this->requirements->getAppVersion(),
            'appVersionNumber' => $this->requirements->getAppVersionNumber(),
            'appCopyright' => $this->requirements->getAppCopyright(),
            'appLizenz' => $this->requirements->getAppLizenz(),
            'page' => 'Template-Übersicht',
        ]);
    }

    /**
     * Show single entity.
     */
    #[Route('/{id}/get', name: 'settings.templates.get', defaults: ['id' => '0'], methods: ['GET'])]
    public function getAction(ManagerRegistry $doctrine, CSRFProtectionService $csrf, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $template = $entityManager->getRepository(Template::class)->find($id);

        $types = $entityManager->getRepository(TemplateType::class)->findAll();

        return $this->render('Templates/templates_form_edit.html.twig', [
            'template' => $template,
            'token' => $csrf->getCSRFTokenForForm(),
            'types' => $types,
        ]);
    }

    /**
     * Show form for new entity.
     */
    #[Route('/new', name: 'settings.templates.new', methods: ['GET'])]
    public function newAction(ManagerRegistry $doctrine, CSRFProtectionService $csrf): Response
    {
        $entityManager = $doctrine->getManager();

        $lastTemplateId = $this->getLastTemplateId($entityManager);
        $template = new Template();

        $template->setId($lastTemplateId[0]->getId() + 1);

        $types = $entityManager->getRepository(TemplateType::class)->findAll();

        return $this->render('Templates/templates_form_create.html.twig', [
            'template' => $template,
            'token' => $csrf->getCSRFTokenForForm(),
            'types' => $types,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/create', name: 'settings.templates.create', methods: ['POST'])]
    public function createAction(
        ManagerRegistry $doctrine,
        CSRFProtectionService $csrf,
        TemplatesService $templatesService,
        Request $request
    ): Response {
        $error = false;
        if ($csrf->validateCSRFToken($request)) {
            $template = $templatesService->getEntityFromForm($request, 'new');

            // check for mandatory fields
            if (0 == strlen($template->getName())) {
                $error = true;
                $this->addFlash('warning', 'flash.mandatory');
            } else {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($template);
                $entityManager->flush();

                // add succes message
                $this->addFlash('success', 'templates.flash.create.success');
            }
        }

        return $this->render('feedback.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/{id}/edit', name: 'settings.templates.edit', defaults: ['id' => '0'], methods: ['POST'])]
    public function editAction(
        ManagerRegistry $doctrine,
        CSRFProtectionService $csrf,
        TemplatesService $templatesService,
        Request $request,
        $id
    ): Response {
        $error = false;
        if ($csrf->validateCSRFToken($request)) {
            $template = $templatesService->getEntityFromForm($request, $id);
            $entityManager = $doctrine->getManager();

            // check for mandatory fields
            if (0 == strlen($template->getName())) {
                $error = true;
                $this->addFlash('warning', 'flash.mandatory');
                // stop auto commit of doctrine with invalid field values
                $entityManager->clear(Template::class);
            } else {
                $entityManager->persist($template);
                $entityManager->flush();

                // add succes message
                $this->addFlash('success', 'templates.flash.edit.success');
            }
        }

        return $this->render('feedback.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/{id}/delete', name: 'settings.templates.delete', methods: ['GET', 'DELETE'])]
    public function deleteAction(
        CSRFProtectionService $csrf,
        TemplatesService $templatesService,
        Request $request,
        Template $template
    ): Response {
        if ('DELETE' == $request->getMethod()) {
            if ($csrf->validateCSRFToken($request, true)) {
                $countCor = $template->getCorrespondences()->count();

                if ($countCor > 0) {
                    $this->addFlash('warning', 'templates.flash.delete.inuse.reservations');
                } else {
                    $template = $templatesService->deleteEntity($template->getId());
                    if ($template) {
                        $this->addFlash('success', 'templates.flash.delete.success');
                    }
                }
            }

            return new Response('', Response::HTTP_NO_CONTENT);
        } else {
            // initial get load (ask for deleting)
            return $this->render('common/form_delete_entry.html.twig', [
                'id' => $template->getId(),
                'token' => $csrf->getCSRFTokenForForm(),
            ]);
        }
    }

    /**
     * Preview single entity.
     */
    #[Route('/{id}/preview', name: 'settings.templates.preview', methods: ['GET'])]
    public function previewAction(ManagerRegistry $doctrine, TemplatesService $templatesService, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $reservation = $entityManager->getRepository(Reservation::class)->find(172);

        $template = $templatesService->renderTemplateForReservations($id, [$reservation]);

        return $this->render('Templates/templates_preview.html.twig', [
            'template' => $template,
        ]);
    }

    /**
     * Called when clicking add conversation in the reservation overview.
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/select/reservation', name: 'settings.templates.select.reservation', methods: ['POST'])]
    public function selectReservationAction(
        ManagerRegistry $doctrine,
        RequestStack $requestStack,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();

        if ('true' == $request->request->get('createNew')) {
            $selectedReservationIds = [];
            $requestStack->getSession()->set('selectedReservationIds', $selectedReservationIds);
        // reset session variables
        // $requestStack->getSession()->remove("invoicePositionsMiscellaneous");
        } else {
            $selectedReservationIds = $requestStack->getSession()->get('selectedReservationIds');
        }

        if (null != $request->request->get('reservationid')) {
            $selectedReservationIds[] = $request->request->get('reservationid');
            $requestStack->getSession()->set('selectedReservationIds', $selectedReservationIds);
        }

        $reservations = [];
        foreach ($selectedReservationIds as $reservationId) {
            $reservations[] = $entityManager->getRepository(Reservation::class)->find($reservationId);
        }

        return $this->render(
            'Templates/templates_form_show_selected_reservations.html.twig',
            [
                'reservations' => $reservations,
            ]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    #[Route('/get/reservations', name: 'settings.templates.get.reservations', methods: ['GET'])]
    public function getReservationsAction(
        ManagerRegistry $doctrine,
        CSRFProtectionService $csrf,
        RequestStack $requestStack,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();

        if ('true' == $request->query->get('createNew')) {
            $selectedReservationIds = [];
            $requestStack->getSession()->set('selectedReservationIds', $selectedReservationIds);
        // reset session variables
        // $requestStack->getSession()->remove("invoicePositionsMiscellaneous");
        } else {
            $selectedReservationIds = $requestStack->getSession()->get('selectedReservationIds');
        }

        if (0 == count($selectedReservationIds)) {
            $objectContainsReservations = 'false';
        } else {
            $objectContainsReservations = 'true';
        }

        return $this->render(
            'Templates/templates_form_select_reservation.html.twig',
            [
                'objectContainsReservations' => $objectContainsReservations,
            ]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    #[Route('/remove/reservation/from/selection', name: 'settings.templates.remove.reservation.from.selection', methods: ['POST'])]
    public function removeReservationFromSelectionAction(
        ManagerRegistry $doctrine,
        RequestStack $requestStack,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();

        $selectedReservationIds = $requestStack->getSession()->get('selectedReservationIds');

        if (null != $request->request->get('reservationkey')) {
            unset($selectedReservationIds[$request->request->get('reservationkey')]);
            $requestStack->getSession()->set('selectedReservationIds', $selectedReservationIds);
        }

        return $this->selectReservationAction($requestStack, $request);
    }

    #[Route('/get/reservations/in/period', name: 'settings.templates.get.reservations.in.period', methods: ['POST'])]
    public function getReservationsInPeriodAction(
        ManagerRegistry $doctrine,
        RequestStack $requestStack,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();
        $reservations = [];
        $selectedReservationIds = $requestStack->getSession()->get('selectedReservationIds');
        $potentialReservations = $entityManager->getRepository(
            Reservation::class
        )->loadReservationsForPeriod($request->request->get('from'), $request->request->get('end'));

        foreach ($potentialReservations as $reservation) {
            // make sure that already selected reservation can not be choosen twice
            if (!in_array($reservation->getId(), $selectedReservationIds)) {
                $reservations[] = $reservation;
            }
        }

        return $this->render(
            'Reservations/reservation_matching_reservations.html.twig',
            [
                'reservations' => $reservations,
            ]
        );
    }

    #[Route('/get/reservations/for/customer', name: 'settings.templates.get.reservations.for.customer', methods: ['POST'])]
    public function getReservationsForCustomerAction(
        ManagerRegistry $doctrine,
        RequestStack $requestStack,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();
        $reservations = [];
        $selectedReservationIds = $requestStack->getSession()->get('selectedReservationIds');

        $customer = $entityManager->getRepository(Customer::class)->findOneByLastname(
            $request->request->get('lastname')
        );

        if ($customer instanceof Customer) {
            $potentialReservations = $entityManager->getRepository(
                Reservation::class
            )->loadReservationsWithoutInvoiceForCustomer($customer);

            foreach ($potentialReservations as $reservation) {
                if (!in_array($reservation->getId(), $selectedReservationIds)) {
                    $reservations[] = $reservation;
                }
            }
        }

        return $this->render(
            'Reservations/reservation_matching_reservations.html.twig',
            [
                'reservations' => $reservations,
            ]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/email/send', name: 'settings.templates.email.send', methods: ['POST'])]
    public function sendEmailAction(
        ManagerRegistry $doctrine,
        CSRFProtectionService $csrf,
        TemplatesService $templatesService,
        RequestStack $requestStack,
        MailService $mailService,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();

        $error = false;
        if ($csrf->validateCSRFToken($request)) {
            $to = $request->request->get('to');
            $subject = $request->request->get('subject');
            $msg = $request->request->get('msg');
            $templateId = $request->request->get('templateId');
            $attachmentIds = $requestStack->getSession()->get('templateAttachmentIds', []);

            // todo add email validation http://silex.sensiolabs.org/doc/providers/validator.html
            if (0 == strlen($to) || 0 == strlen($subject) || 0 == strlen($msg)) {
                $error = true;
                $this->addFlash('warning', 'flash.mandatory');
            } else {
                $attachments = [];
                // add attachments
                foreach ($attachmentIds as $attId) {
                    $id = reset($attId); // first element of array
                    $mailAttachment = $templatesService->getMailAttachment($id);
                    if (null !== $mailAttachment) {
                        $attachments[] = $mailAttachment;
                    }
                }

                $mailService->sendHTMLMail($to, $subject, $msg, $attachments);

                // now save correspondence to db
                $template = $entityManager->getReference(Template::class, $templateId);

                // associate with reservations
                $reservations = $templatesService->getReferencedReservationsInSession();

                // save correspondence for each reservation
                foreach ($reservations as $reservation) {
                    $mail = new MailCorrespondence();
                    $mail->setRecipient($to)
                         ->setName($subject)
                         ->setSubject($subject)
                         ->setText($msg)
                         ->setTemplate($template)
                        ->setReservation($reservation);

                    // add connection to attachments
                    foreach ($attachmentIds as $attId) {
                        $child = $entityManager->getReference(Correspondence::class, $attId[$reservation->getId()]);
                        $mail->addChild($child);
                    }
                    $entityManager->persist($mail);
                    $entityManager->flush();
                }

                $this->addFlash('success', 'templates.sendemail.success');
            }
        } else {
            $this->addFlash('warning', 'flash.invalidtoken');
            $error = true;
        }

        return $this->render('feedback.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/file/save', name: 'settings.templates.file.save', methods: ['POST'])]
    public function saveFileAction(
        ManagerRegistry $doctrine,
        CSRFProtectionService $csrf,
        TemplatesService $templatesService,
        RequestStack $requestStack,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();

        $error = false;
        if ($csrf->validateCSRFToken($request)) {
            $subject = $request->request->get('subject');
            $msg = $request->request->get('msg');
            $templateId = $request->request->get('templateId');

            if (0 == strlen($subject) || 0 == strlen($msg)) {
                $error = true;
                $this->addFlash('warning', 'flash.mandatory');
            } else {
                // if this file is an attachment for email,
                $attachmentForId = $requestStack->getSession()->get('selectedTemplateId', null);

                // now save correspondence to db
                $template = $entityManager->getReference(Template::class, $templateId);

                // associate with reservations
                $reservations = $templatesService->getReferencedReservationsInSession();

                // save correspondence for each reservation
                foreach ($reservations as $reservation) {
                    $file = new FileCorrespondence();
                    $file->setFileName($subject)
                         ->setName($subject)
                         ->setText($msg)
                         ->setTemplate($template)
                         ->setReservation($reservation);
                    $entityManager->persist($file);
                    $entityManager->flush();
                }

                $isAttachment = false;
                if (null != $attachmentForId) {
                    $templatesService->addFileAsAttachment($file->getId(), $reservations);
                    $isAttachment = true;
                    $error = true;  // just to enable flash message in modal
                }

                $this->addFlash('success', 'templates.savefile.success');
            }
        } else {
            $this->addFlash('warning', 'flash.invalidtoken');
            $error = true;
        }

        return $this->render('feedback.html.twig', [
            'error' => $error,
            'attachment' => $isAttachment,
        ]);
    }

    /**
     * Softly delete attachment, doesn't delete file from db.
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    #[Route('/attachment/remove', name: 'settings.templates.attachment.remove', methods: ['POST'])]
    public function deleteAttachmentAction(
        ManagerRegistry $doctrine,
        CSRFProtectionService $csrf,
        RequestStack $requestStack,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();

        $error = false;
        if ($csrf->validateCSRFToken($request)) {
            $aId = $request->request->get('id');
            $attachments = $requestStack->getSession()->get('templateAttachmentIds');
            $isAttachment = false;
            // loop through all reservations
            foreach ($attachments as $key => $attachment) {
                // search for attachment id and delte entry if it exists
                $rId = array_search($aId, $attachment);
                if (false !== $key) {
                    unset($attachments[$key][$rId]);
                    $isAttachment = true;
                }
                // just remove empty arrays
                if (0 == count($attachments[$key])) {
                    unset($attachments[$key]);
                }
            }

            if ($isAttachment) {
                $requestStack->getSession()->set('templateAttachmentIds', $attachments);
            // $correspondence = $entityManager->getReference(Correspondence::class, $aId);
            } else {
                $this->addFlash('warning', 'templates.attachment.notfound');
                $error = true;
            }
        } else {
            $this->addFlash('warning', 'flash.invalidtoken');
            $error = true;
        }

        return $this->render('feedback.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/correspondence/remove', name: 'settings.templates.correspondence.remove', methods: ['POST'])]
    public function deleteCorrespondenceAction(
        ManagerRegistry $doctrine,
        CSRFProtectionService $csrf,
        Request $request
    ): Response {
        $entityManager = $doctrine->getManager();

        $error = false;
        if ($csrf->validateCSRFToken($request, true)) {
            $cId = $request->request->get('id');
            $correspondence = $entityManager->getRepository(Correspondence::class)->find($cId);

            if ($correspondence instanceof Correspondence) {
                $entityManager->remove($correspondence);
                $entityManager->flush();
                $this->addFlash('success', 'templates.correspondence.delete.ok');
            } else {
                $this->addFlash('warning', 'templates.correspondence.notfound');
                $error = true;
            }
        } else {
            $this->addFlash('warning', 'flash.invalidtoken');
            $error = true;
        }

        return $this->render('feedback.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * Adds an already added file (correspondence) as attachment of the current mail.
     */
    #[Route('/attachment/add', name: 'settings.templates.attachment.add', methods: ['POST'])]
    public function addAttachmentAction(
        TemplatesService $templatesService,
        Request $request,
        InvoiceService $invoiceService
    ): Response {
        $error = false;
        $isInvoice = $request->request->get('isInvoice', 'false');
        $cId = $request->request->get('id');
        if ('false' != $isInvoice) {
            $cId = $templatesService->makeCorespondenceOfInvoice($cId, $invoiceService);
        }

        $reservations = $templatesService->getReferencedReservationsInSession();
        $templatesService->addFileAsAttachment($cId, $reservations);

        return $this->render('feedback.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route('/correspondence/export/pdf/{id}/', name: 'settings.templates.correspondence.export.pdf', defaults: ['id' => '0'], methods: ['GET'])]
    public function exportPDFCorrespondenceAction(
        ManagerRegistry $doctrine,
        TemplatesService $templatesService,
        Request $request,
        $id
    ): Response {
        $entityManager = $doctrine->getManager();
        $correspondence = $entityManager->getRepository(Correspondence::class)->find($id);
        if ($correspondence instanceof FileCorrespondence) {
            $output = $templatesService->getPDFOutput(
                $correspondence->getText(),
                $correspondence->getName(),
                $correspondence->getTemplate()
            );
            $response = new Response($output);
            $response->headers->set('Content-Type', 'application/pdf');

            return $response;
        }

        return new Response('no file');
    }

    #[Route('/correspondence/show/{id}', name: 'settings.templates.correspondence.show', defaults: ['id' => '0'], methods: ['POST'])]
    public function showMailCorrespondenceAction(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $correspondence = $entityManager->getRepository(Correspondence::class)->find($id);
        if ($correspondence instanceof MailCorrespondence) {
            return $this->render(
                'Templates/templates_show_mail.html.twig',
                [
                    'correspondence' => $correspondence,
                    'reservationId' => $request->request->get('reservationId'),
                ]
            );
        }

        return new Response('no mail');
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    #[Route('/editortemplate/{templateTypeId}', name: 'settings.templates.editor.template', defaults: ['templateTypeId' => '1'], methods: ['GET'])]
    public function getTemplatesForEditor(ManagerRegistry $doctrine, $templateTypeId): JsonResponse|Response
    {
        $entityManager = $doctrine->getManager();
        /* @var $type TemplateType */
        $type = $entityManager->getRepository(TemplateType::class)->find($templateTypeId);
        if ($type instanceof TemplateType && !empty($type->getEditorTemplate())) {
            $response = $this->render('Templates/'.$type->getEditorTemplate());
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = $this->json([]);
        }

        return $response;
    }

    #[Route('/upload', name: 'templates.upload', methods: ['POST'])]
    public function uploadImage(Request $request, FileUploader $fos): JsonResponse|Response
    {
        /** @var UploadedFile $imageFile */
        $imageFile = $request->files->get('file');
        if (!$fos->isValidImage($imageFile)) {
            return new Response('', Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        try {
            $name = $fos->upload($imageFile);
        } catch (FileException $ex) {
            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $path = rtrim($request->getBasePath(), '/').'/'.$fos->getPublicDirectory().'/'.$name;

        return $this->json([
            'location' => $path,
            ]);
    }

    public function getLastTemplateId($entityManager): array
    {
        $customerTemplate = $entityManager
            ->getRepository(Template::class);

        return $customerTemplate->findBy([], ['id' => 'DESC'], 1, 0);
    }
}
