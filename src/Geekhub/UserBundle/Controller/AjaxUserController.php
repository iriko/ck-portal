<?php

namespace Geekhub\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Geekhub\UserBundle\Entity\User;

class AjaxUserController extends Controller
{
    public function updateUserAboutMeAction(Request $request)
    {
        if (is_array($error = $this->isValidRequest())) {
            return $this->prepareAjaxResponse($error);
        }

        $user = $this->getUser();
        $user->setAboutMe($request->get('aboutMe'));

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) == 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->prepareAjaxResponse(array('success' => 'Your information has been update successfully'));
        }

        return $this->prepareAjaxResponse(array('error' => $errors));
    }

    public function updateUserAvatarAction(Request $request)
    {
        if (is_array($error = $this->isValidRequest())) {
            return $this->prepareAjaxResponse($error);
        }

        $user = $this->getUser();
        $user->setProfilePicture($request->get('profilePicture'));

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) == 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->prepareAjaxResponse(array('success' => 'Your information has been update successfully'));
        }

        return $this->prepareAjaxResponse(array('error' => $errors));
    }

    public function updateUserSkypeAction(Request $request)
    {
        if (is_array($error = $this->isValidRequest())) {
            return $this->prepareAjaxResponse($error);
        }

        $user = $this->getUser();
        $user->setSkype($request->get('contactData'));

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) == 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->prepareAjaxResponse(array('success' => 'Your information has been update successfully'));
        }

        return $this->prepareAjaxResponse(array('error' => $errors));
    }

    public function updateUserPhoneAction(Request $request)
    {
        if (is_array($error = $this->isValidRequest())) {
            return $this->prepareAjaxResponse($error);
        }

        $user = $this->getUser();
        $user->setPhone($request->get('contactData'));

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) == 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->prepareAjaxResponse(array('success' => 'Your information has been update successfully'));
        }

        return $this->prepareAjaxResponse(array('error' => $errors));
    }

    public function updateUserEmailAction(Request $request)
    {
        if (is_array($error = $this->isValidRequest())) {
            return $this->prepareAjaxResponse($error);
        }

        $user = $this->getUser();
        $user->setEmail($request->get('contactData'));

        $validator = $this->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) == 0) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->prepareAjaxResponse(array('success' => 'Your information has been update successfully'));
        }

        return $this->prepareAjaxResponse(array('error' => $errors));
    }

    public function sendEmailAction(Request $request)
    {
        if (is_array($error = $this->isValidRequest())) {
            return $this->prepareAjaxResponse($error);
        }

        $senderEmail = $request->get('senderEmail');
        $themeEmail = $request->get('themeEmail');
        $textEmail = $request->get('textEmail');
        $slug = $request->get('userSlug');

        $em = $this->getDoctrine()->getManager();
        /* @var $recipientUser User */
        $recipientUser = $em->getRepository('UserBundle:User')->findOneBySlug($slug);

        $message = \Swift_Message::newInstance()
            ->setSubject('Повідомлення з сайту Черкаська мрія')
            ->setFrom('noreplay@chedream.com')
            ->setTo($recipientUser->getEmail())
            ->setBody(
                $this->renderView(
                    'UserBundle:Email:message.html.twig',
                    array(
                        'themeEmail'    => $themeEmail,
                        'senderEmail'   => $senderEmail,
                        'textEmail'     => $textEmail,
                        'senderUser'    => $this->getUser(),
                        'recipientUser' => $recipientUser
                    )
                )
            )
        ;
        $this->get('mailer')->send($message);

        return $this->prepareAjaxResponse(array('success' => 'Your email has been send successfully!'));
    }

    /**
     * @param $answer array message ('error' => 'This error')
     * @param $format string type of response format - json, xml
     * @return $response Response
     */
    private function prepareAjaxResponse($answer, $format = 'json')
    {
        $jsonError = $this->container->get('serializer')->serialize($answer, $format);
        $response = new Response($jsonError);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function isValidRequest()
    {
        $securityContext = $this->container->get('security.context');
//        if (false == $this->getRequest()->isXmlHttpRequest())
//        {
//            return array('error' => 'Only ajax request can be send');
//        }
        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return array('error' => 'Only authenticated user can make donate');
        }

        return true;
    }
}