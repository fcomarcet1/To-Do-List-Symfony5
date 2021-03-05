<?php
namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskManager
{

    private EntityManagerInterface $em;
    private TaskRepository $taskRepository;
    private ValidatorInterface $validator;

    /**
     * TaskManager constructor.
     * @param EntityManagerInterface $em
     * @param TaskRepository $taskRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $em,
        TaskRepository $taskRepository,
        ValidatorInterface $validator
    )
    {
        $this->em = $em;
        $this->taskRepository = $taskRepository;
        $this->validator = $validator;
    }


    /**
     * @param Task $task
     */
    public function createService(Task $task): void
    {
        $this->em->persist($task);
        $this->em->flush();
    }

    /**
     * @param Task $task
     */
    public function updateService(Task $task): void
    {
        $this->em->flush();
    }

    /**
     * @param Task $task
     */
    public function deleteService(Task $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }


    /**
     * @param Task $task
     * @return ConstraintViolationList
     */
    public function validateTask(Task $task): ConstraintViolationList
    {

        // Con el bundle validator
        $errors = $this->validator->validate($task);
        return $errors;


        // Metodo clasico
        /*if (empty($tarea->getDescripcion()))
            $errores[] = "Campo 'descripción' obligatorio";

        $tareaCondescripcionIgual = $this->tareaRepository->buscarTareaPorDescripcion($tarea->getDescripcion());
        if (null !== $tareaCondescripcionIgual && $tarea->getId() !== $tareaCondescripcionIgual->getId()) {
            $errores[] = "Descripción repetida";
        }*/


    }

}