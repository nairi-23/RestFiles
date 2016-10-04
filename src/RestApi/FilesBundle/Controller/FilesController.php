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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
/**
 * Description of FilesController
 *
 * @author nairi
 */
class FilesController extends Controller {


     
    public function addFileAction($id = null, Request $request) {
        
         if ($request->isMethod('POST')) {
            return $this->fileGeneratorAction($id, $request);
        }
    }

    public function fileGeneratorAction($id = null, Request $request) {


        $insert_data = new FileModel();
        $form = $this->createForm(FileModelType::class, $insert_data);
        $form->handleRequest($request);
        $file = $insert_data->getFile();

        if (!is_null($file)) {
            $file_hash_name = md5(uniqid()) . '.' . $file->guessExtension();

            $date = date("Y-m-d m:s:i");
            $date = new\DateTime($date);

            $file->move(
                    'files', $file_hash_name
            );
             if (!is_null($id)) {
                $em = $this->getDoctrine()->getManager();
                $DBdata = $em->getRepository('RestApiFilesBundle:FileModel')->find($id);
            } else {
                $insert_data->setCreated($date);
                $em = $this->getDoctrine()->getManager();
            }
            $insert_data->setUpdated($date);
            $insert_data->setFileHashName($file_hash_name);
            $em->persist($insert_data);

            $em->flush();
            $message = "{'message':'File created'}";
            echo $message."\n";
            return $this->gitProcessAction();
        } else {
            $message = "{'message':'Process ended'}";
            return new Response($message);
        }
    }

    public function AllFilesAction() {
        
        $allFiles = $this->getDoctrine()->getManager();
        $file = $allFiles->getRepository('RestApiFilesBundle:FileModel')->findAll();
        $serializer = $this->get('jms_serializer');
        $response = $serializer->serialize($file, 'json');
        return new Response($response);
        
    }

    public function ControlRequestAction($id, Request $request) {

        switch ($request) {
            case $request->isMethod("DELETE"):
                $this->deleteAction($id);
                break;
            case $request->isMethod("GET"):
                $this->showFileAction($id);
                break;
            case $request->isMethod("PUT") || $request->isMethod("PATCH"):
                $this->editAction($id, $request);
                break;
        }
    }
    public function gitProcessAction(){
        $process = new Process("git commit -a -m 'commit'");
           
                try {
                    $process->setPty(true);
                    $process->mustRun(function ($type, $buffer) {
                        echo $buffer;
                        $process2 = new Process("git push");
                         try {
                                $process2->setPty(true);
                                $process2->mustRun(function ($type, $buffer) {
                                    echo $buffer;
                                    $process2 = new Process("git push");
                                    die();
                                });
                             } catch (ProcessFailedException $e) {
                                 
                         }
                       
                     });
                 } catch (ProcessFailedException $e) {
             }
    }
    public function deleteAction($id) {

        if (!$id) {
            throw $this->createNotFoundException('id not found');
        }
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository('RestApiFilesBundle:FileModel')->find($id);
        if (!is_null($file)) {
            $em->remove($file);
            $em->flush();
            if(file_exists($file->getFileHashName())){
                
            unlink('files/' . $file->getFileHashName());
            }
            $message = "{'message':'Removed File" . $file->getFileHashName() . "'}";
            echo $message."\n";
            return $this->gitProcessAction();
        } else {
            $message = "{'message':'File not exist'}";
            echo $message;
            die();
        }
    }

    public function editAction($id, $request) {
        
        $content = file_get_contents("php://input");
        $em = $this->getDoctrine()->getManager();
        $updated_data = $em->getRepository('RestApiFilesBundle:FileModel')->find($id);
        if (!is_null($updated_data)) {
            $date = date("Y-m-d m:s:i");
            $date = new\DateTime($date);
      
            $updated_data->setUpdated($date);
            $fileName = $updated_data->getFileHashName();
            $em->flush();
            $file = fopen("files/" . $fileName . ".txt", "w");
            fwrite($file, $content);
            fclose($file);
            $message = "{'message':'File updated'}";
            echo $message."\n";
            return $this->gitProcessAction();
        } else {
            $message = "{'message':'File not exist'}";
            echo $message;
            die();
        }
    }

    public function showFileAction($id) {

        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository('RestApiFilesBundle:FileModel')->find($id);

        if (!is_null($file)) {
            $serializer = $this->get('jms_serializer');
            $response = $serializer->serialize($file, 'json');
            echo $response;
            die();
        } else {
            $message = "{'message':'not exist file'}";
            echo $message;
            die();
        }
    }

}
