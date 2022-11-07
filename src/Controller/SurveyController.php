<?php

namespace WebWMS\Controller;

use WebWMS\Entity\Group;
use WebWMS\Entity\Survey;
use WebWMS\Repository\SurveyRepository;
use WebWMS\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SurveyController extends AbstractController
{
    public function __construct(
        private SurveyRepository $surveyRepository,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @param Group $group
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/survey/group/{id}', name: 'create-survey')]
    //#[IsGranted((array)'lb_survey create node survey', subject: 'group')]
    public function create(Group $group, Request $request): Response
    {
        //$this->denyAccessUnlessGranted('can_create_survey', $group);

        try {
            $name = $request->get('name');

            if (empty($name)) {
                throw new EntityNotFoundException("User id or survey name can not be null!");
            }

            $survey = new Survey();
            $survey->setName($name);
            $survey->setGroup($group);
            $surveyId = $this->surveyRepository->save($survey);

            return $this->json([
              "surveyId" => $surveyId,
              "message" => "Survey created successfully",
            ]);
        } catch (\Throwable $e) {
            return $this->json(["error_message" => $e->getMessage()]);
        }
    }


    /**
     * @param Survey $survey
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/survey/{id}/edit', name: 'edit-survey')]
    public function edit(Survey $survey, Request $request): Response
    {
        $this->denyAccessUnlessGranted('lb_survey edit node survey', $survey->getGroup());

        try {
            $name = $request->get('name');

            if (empty($name)) {
                throw new EntityNotFoundException("User id or survey name can not be null!");
            }

            $survey->setName($name);
            $survey->setGroup($survey->getGroup());
            $surveyId = $this->surveyRepository->save($survey);

            return $this->json([
              "surveyId" => $surveyId,
              "message" => "Survey created successfully",
            ]);
        } catch (\Throwable $e) {
            return $this->json(["error_message" => $e->getMessage()]);
        }
    }
}
