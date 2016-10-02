<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace RestApi\FilesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use RestApi\FilesBundle\Entity\FileModel;
use RestApi\FilesBundle\Form\FileModelType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of FilesController
 *
 * @author nairi
 */
class FilesController extends Controller {

    public function addFileAction(Request $request) {

        $form_tamplate = new FileModel();
        $form = $this->createForm(FileModelType::class, $form_tamplate);
        $form->handleRequest($request);
        if ($this->getRequest()->isMethod('POST')) {

            $file = $form_tamplate->getFile();
            if (!is_null($file)) {
                $file_hash_name = md5(uniqid()) . '.' . $file->guessExtension();
                $file_name = $form_tamplate->getName();
                $date = date("Y-m-d m:s:i");
                $date = new\DateTime($date);
                
                $file->move(
                        'files', 
                        $file_hash_name 
                );
                shell_exec("git pull");
                shell_exec("git commit");
                shell_exec("git push");
                $form_tamplate->setName($file_name);
                $form_tamplate->setCreated($date);
                $form_tamplate->setUpdated($date);
                $form_tamplate->setFileHashName($file_hash_name);

                $em = $this->getDoctrine()->getManager();

                $em->persist($form_tamplate);

                $em->flush();
                $message = "{'message':'File created'}";
                return new Response($message);
            }else{
                $message = "{'message':'check your data property. file name - 'file model[file]' and name its -'file_model[name]'}";
                return new Response($message);
            }
        }

        return $this->render('RestApiFilesBundle:Files:files.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    public function allFilesJSONAction() {

        $allFiles = $this->getDoctrine()->getManager();
        $file = $allFiles->getRepository('RestApiFilesBundle:FileModel')->findAll();
        $serializer = $this->get('jms_serializer');
        $response = $serializer->serialize($file, 'json');
        return new Response($response);
    }

    public function AllFilesAction() {

        $allFiles = $this->getDoctrine()->getManager();
        $file = $allFiles->getRepository('RestApiFilesBundle:FileModel')->findAll();

        return $this->render('RestApiFilesBundle:Files:allFiles.html.twig', array(
                    'files' => $file,
        ));
    }

    public function deleteAction($id, Request $request) {

        if (!$id) {
            throw $this->createNotFoundException('No id found');
        }
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository('RestApiFilesBundle:FileModel')->find($id);
        if (!is_null($file)) {
            $serializer = $this->get('jms_serializer');
            $response = $serializer->serialize($file, 'json');
            if ($request->isMethod("DELETE")) {
                $em->remove($file);
                $em->flush();
                return new Response("Removed File" . $response);
            } else {
                return $this->editDeleteAction($id, $request);
            }
        } else {
            $message = "{'message':'not exist file'}";
            return new Response($message);
        }
    }

    public function editDeleteAction($id, Request $request) {

        if ($request->isMethod("DELETE")) {
            return $this->deleteAction($id, $request);
        } else if($request->isMethod("GET")){
            $form_tamplate = new FileModel();
            $form = $this->createForm(FileModelType::class, $form_tamplate);
            $em = $this->getDoctrine()->getManager();
            $file = $em->getRepository('RestApiFilesBundle:FileModel')->find($id);
            if (!is_null($file)) {
                $serializer = $this->get('jms_serializer');
                $response = $serializer->serialize($file, 'json');
                echo $response;
                return $this->render("RestApiFilesBundle:Files:fileEdit.html.twig", array(
                            'form' => $form->createView(), "id" => $id,
                ));
            } else {
                $message = "{'message':'not exist file'}";
                return new Response($message);
            }
        }else if($request->isMethod("PUT") || $request->isMethod("PATCH") || $request->isMethod("POST")){
            var_dump($request);
            die();
        }
    }

}
