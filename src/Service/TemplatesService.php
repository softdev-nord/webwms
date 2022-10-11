<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use WebWMS\Entity\MailAttachment;
use WebWMS\Entity\Template;
use WebWMS\Entity\TemplateType;
use WebWMS\Interfaces\ITemplateRenderer;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        TemplatesService
 */
class TemplatesService
{
    public function __construct(
        private string $webHost,
        private Environment $twig,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private MpdfService $mpdfs
    ) {
    }

    /**
     * Extract form data and return Template object.
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getEntityFromForm(Request $request, string $id = 'new'): Template
    {
        $template = new Template();
        if ('new' !== $id) {
            $template = $this->entityManager->getRepository(Template::class)->find($id);
        }
        $templateId = $request->request->get('type-'.$id);
        $type = $this->entityManager->getRepository(TemplateType::class)->find($templateId);
        if (!($type instanceof TemplateType)) {
            // throw ""
        }
        $template->setTemplateType($type);
        $template->setName(trim($request->request->get('name-'.$id)));
        $template->setText($request->request->get('text-'.$id));
        $template->setParams($request->request->get('params-'.$id));
        if ($request->request->has('default-'.$id)) {
            $template->setIsDefault(true);
        } else {
            $template->setIsDefault(false);
        }

        return $template;
    }

    /**
     * Delete entity.
     */
    public function deleteEntity(int $id): bool
    {
        $template = $this->entityManager->getRepository(Template::class)->find($id);

        $this->entityManager->remove($template);
        $this->entityManager->flush();

        return true;
    }

    public function renderTemplateForReservations($templateId, $reservations): string
    {
        /* @var $template Template */
        $template = $this->entityManager->getRepository(Template::class)->find($templateId);

        $templateStr = $this->twig->createTemplate($template->getText());

        return $templateStr->render([
            'reservations' => $reservations,
        ]);
    }

    public function renderTemplate(int $templateId, mixed $param, ITemplateRenderer $serviceObj): string
    {
        /* @var $template Template */
        $template = $this->entityManager->getRepository(Template::class)->find($templateId);

        $params = [];
        $service = $template->getTemplateType()->getService();
        if (!empty($service)) {
            // each service must implement the ITemplateRenderer interface
            $params = $serviceObj->getRenderParams($template, $param);
        }

        $str = $this->replaceTwigSyntax($template->getText());
        $templateStr = $this->twig->createTemplate($str);

        return $templateStr->render($params);
    }

    public function addFileAsAttachment($cId, $reservations): bool
    {
        $fileIds = [];

        foreach ($reservations as $reservation) {
            // save file ids in context of reservation id
            $fileIds[$reservation->getId()] = $cId;
        }

        $attachments = $this->requestStack->getSession()->get('templateAttachmentIds');
        $attachments[] = $fileIds;
        $this->requestStack->getSession()->set('templateAttachmentIds', $attachments);

        return true;
    }

    /**
     * Returns a MailAttachment entity which can be passed to Mailer.
     */
//    public function getMailAttachment($attachmentId): ?MailAttachment
//    {
//        /* @var $attachment \WebPMS\Entity\Correspondence */
//        $attachment = $this->entityManager->getRepository(Correspondence::class)->find($attachmentId);
//        if ($attachment instanceof FileCorrespondence) {
//            $data = $this->getPDFOutput($attachment->getText(), $attachment->getName(), $attachment->getTemplate(), true);
//
//            return new MailAttachment($data, $attachment->getName().'.pdf', 'application/pdf');
//        }
//
//        return null;
//    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getPDFOutput($input, $name, $template, $noResponseOutput = false)
    {
        /*
         * I: send the file inline to the browser. The plug-in is used if available. The name given by filename is used when one selects the "Save as" option on the link generating the PDF.
         * D: send to the browser and force a file download with the name given by filename.
         * F: save to a local file with the name given by filename (may include a path).
         * S: return the document as a string. filename is ignored.
         */
        $dest = ($noResponseOutput ? 'S' : 'D');
        $mpdf = $this->mpdfs->getMpdf();

        $params = json_decode($template->getParams());
        $mpdf->addPage(
            $params->orientation,
            '',
            '',
            '',
            '',
            $params->marginLeft,
            $params->marginRight,
            $params->marginTop,
            $params->marginBottom,
            $params->marginHeader,
            $params->marginFooter
        );

        $inputMapped = $this->mapImageSrc($input);

        /*
         * mode
         * 0 - Use this (default) if the text you pass is a complete HTML page including head and body and style definitions.
         * 1 - Use this when you want to set a CSS stylesheet
         * 2 - Write HTML code without the <head> information. Does not need to be contained in <body>
         */
        $mpdf->WriteHTML($inputMapped, 0);

        return $mpdf->Output($name.'.pdf', $dest);
    }

    /**
     * This maps the src of images to the real web host.
     * This is sometimes needed e.g. when using it with docker.
     * The docker web host is "web" when the application is requested via "localhost" in the browser,
     * mpdf uses the host from the request, which is localhost. But there is no web server listening on localhost in the php container.
     * That's why we need to change the src to the real host "web".
     *
     * @param string $input
     *
     * @return string
     */
    private function mapImageSrc(string $input): string
    {
        $host = rtrim($this->webHost, '/').'/';

        return preg_replace('/src="\/(.*)"/i', 'src="'.$host.'$1"', $input);
    }

    /**
     * Returns the default Template or null.
     */
    public function getDefaultTemplate(array $templates): ?Template
    {
        // find default template
        foreach ($templates as $template) {
            if ($template->getIsDefault()) {
                return $template;
            }
        }

        return null;
    }

    private function replaceTwigSyntax(string $string): string
    {
        $template1 = str_replace('[[', '{{', $string);
        $template2 = str_replace(']]', '}}', $template1);

        $template3 = str_replace('[%', '{%', $template2);
        $template4 = str_replace('%]', '%}', $template3);

        $template5 = str_replace('[#', '{#', $template4);
        $template6 = str_replace('#]', '#}', $template5);

        $template7 = preg_replace(
            "/<div class=\"footer\">(.*)<\/div>/s",
            '<htmlpagefooter name="footer">$1</htmlpagefooter><sethtmlpagefooter name="footer" value="on"></sethtmlpagefooter>',
            $template6
        );

        return $template7;
    }
}
