<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Service\TaskManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    /**
     * @Route("/", name="app_tasks")
     * @param TaskRepository $taskRepository
     * @return Response
     */
    public function Tasks(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        //$tasks = $taskRepository->findBy([], ['id' => 'DESC']);

        return $this->render('task/tasks.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $tasks,
        ]);
    }


    /**
     * @Route("/tarea/crear", name="app_create_task")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $description = $request->request->get('description', null);

        if ($description !== null) {
            if (!empty($description)){
                $em = $this->getDoctrine()->getManager();
                $task->setDescription($description);
                $em->persist($task);
                $em->flush();
                $this->addFlash('success', 'Tarea creada correctamente');

                return $this->redirectToRoute('app_tasks');
            }
            else{
                $this->addFlash('warning', 'El campo descripcion no puede estar vacio');
            }
        }
        return $this->render('task/create.html.twig', [
            'task' => $task
        ]);
    }


    /**
     * @Route("/tarea/editar/{id}", name="app_edit_task")
     * @param int $id
     * @param TaskRepository $taskRepository
     * @param Request $request
     * @return Response
     */
    public function edit(int $id, TaskRepository $taskRepository, Request $request): Response
    {
        // Find the task to update
        $task = $taskRepository->find($id);

        // Check if exists the task with this id
        if (!$task){
            throw $this->createNotFoundException();
        }

        // Check if the description ins not null && not empty
        $description = $request->request->get('description', null);
        if ($description !== null) {
            if (!empty($description)){
                $em = $this->getDoctrine()->getManager();
                $task->setDescription($description);
                $em->persist($task);
                $em->flush();
                $this->addFlash('success', 'Tarea creada correctamente');

                return $this->redirectToRoute('app_tasks');
            }
            else{
                $this->addFlash('warning', 'El campo descripcion no puede estar vacio');
            }
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
        ]);
    }


    /**
     * Con paramsConvert
     * @Route(
     *      "/tarea/editar-convert-param/{id}",
     *      name="app_edit_task_convert-param",
     * )
     * @param Task $task
     * @param Request $request
     * @return Response
     */
    public function editParamsConvert(Task $task, Request $request): Response
    {
        $descripcion = $request->request->get('description', null);
        if (null !== $descripcion) {
            if (!empty($descripcion)) {
                $em = $this->getDoctrine()->getManager();
                $task->setDescription($descripcion);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Tarea editada correctamente!'
                );
                return $this->redirectToRoute('app_tasks');
            } else {
                $this->addFlash(
                    'warning',
                    'El campo "Descripción" es obligatorio'
                );
            }
        }
        return $this->render('task/edit.html.twig', [
            "task" => $task,
        ]);
    }


    /**
     * @Route("/tarea/eliminar/{id}", name="app_delete_task")
     * @param Task $task
     * @return Response
     */
    public function delete(Task $task): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();
        $this->addFlash(
            'success',
            'Tarea eliminada correctamente!'
        );

        return $this->redirectToRoute('app_tasks');
    }


    // ------------------- Methods with Task services ----------------------------------

    /**
     * @Route("/crear/tarea-servicio", name="app_create_task_service")
     * @param TaskManager $taskManager
     * @param Request $request
     * @return Response
     */
    public function createService(TaskManager $taskManager, Request $request): Response
    {
        $description = $request->request->get('description', null);
        $task = new Task();
        if (null !== $description) {
            $task->setDescription($description);
            $errors = $taskManager->validateTask($task);

            if (empty($errors)) {
                $taskManager->createService($task);
                $this->addFlash(
                    'success',
                    'Tarea creada correctamente!'
                );
                return $this->redirectToRoute('app_tasks');
            } else {
                foreach ($errors as $error) {
                    $this->addFlash(
                        'warning',
                        $error->getMessage()
                    );
                }
            }
        }
        return $this->render('task/create.html.twig', [
            "task" => $task,
        ]);
    }


    /**
     * Con paramsConvert
     * @Route(
     *      "/editar/tarea-servicio/{id}",
     *      name="app_edit_task_service-params-convert",
     *      requirements={"id"="\d+"}
     * )
     * @param TaskManager $taskManager
     * @param Task $task
     * @param Request $request
     * @return Response
     */
    public function updateParamsConvertService(TaskManager $taskManager, Task $task, Request $request): Response
    {
        $description = $request->request->get('description', null);
        if (null !== $description) {
            $task->setDescription($description);
            $errors = $taskManager->validateTask($task);

            if (0 === count($errors)) {
                $taskManager->updateService($task);
                $this->addFlash(
                    'success',
                    'Tarea actualizada correctamente!'
                );
                return $this->redirectToRoute('app_tasks');
            } else {
                foreach ($errors as $error) {
                    $this->addFlash(
                        'warning',
                        $error->getMessage()
                    );
                }
            }
        }
        return $this->render('task/edit.html.twig', [
            "task" => $task,
        ]);
    }

    /**
     * Con paramsConvert
     * @Route(
     *      "/eliminar/tarea-servicio/{id}",
     *      name="app_delete_task_service"
     *
     * )
     * @param Task $task
     * @param TaskManager $taskManager
     * @return Response
     */
    public function deleteParamsConvertService(Task $task, TaskManager $taskManager): Response
    {
        $taskManager->deleteService($task);
        $this->addFlash(
            'success',
            'Tarea eliminada correctamente!'
        );

        return $this->redirectToRoute('app_tasks');
    }

}
