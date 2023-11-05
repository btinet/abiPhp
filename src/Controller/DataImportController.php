<?php

namespace App\Controller;


use App\Entity\Pupil;
use App\Form\PupilImportType;
use App\Repository\TeacherRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/data', name: 'app_data_')]
class DataImportController extends AbstractController
{

    #[Route('/import', name: 'import', methods: ['GET', 'POST'])]
    public function import(Request $request, TeacherRepository $teacherRepository): Response
    {

        $teachers = $teacherRepository->findAll();
        $csv = null;
        $originalFileName = null;
        $fileContent = [];

        $form = $this->createForm(PupilImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $csvFile */
            $csvFile = $form->get('csv_file')->getData();
            //die($csvFile->getClientMimeType());
            $originalFileName = $csvFile->getClientOriginalName();


            $row = 1;
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($csvFile->getClientMimeType() == 'text/csv') {

                $csvFile->move($this->getParameter('upload_directory'),$csvFile->getFilename());

                if (($handle = fopen($this->getParameter('upload_directory').'/'.$csvFile->getFilename(), "r")) != FALSE) {
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        if($row !== 1) {
                            $fileContent[$row][] = $data[0];
                            $fileContent[$row][] = $data[1];
                            $fileContent[$row][] = $data[2];
                            $fileContent[$row][] = $data[3];
                            $fileContent[$row][] = $data[4];
                        }
                        $row++;
                    }
                    fclose($handle);
                    $csv = $this->getParameter('upload_directory').'/'.$csvFile->getFilename();
                }
            } else {
                $this->addFlash('error','Es wurde keine CSV-Datei angegeben.');
            }
        }

        return $this->render('data/import.html.twig', [
            'local_nav' => 'import',
            'form' => $form,
            'file_content' => $fileContent,
            'file' => $csv,
            'origin_file' => $originalFileName,
            'teachers' => $teachers
        ]);
    }

    #[Route('/import/persist', name: 'import_persist', methods: ['POST'])]
    public function persist(Request $request, TeacherRepository $teacherRepository, EntityManagerInterface $entityManager): Response
    {
        $row = 1;
        $csvFile = $request->request->get('data');
        $teacherId = $request->request->get('teacher');
        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded

            if (($handle = fopen($csvFile, "r")) != FALSE) {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    if($row>1){
                        $pupil = new Pupil();
                        $pupil->setFirstname($data[0]);
                        $pupil->setLastname($data[1]);
                        try {
                            if($data[2] != null) {
                                $date = DateTime::createFromFormat('d.m.Y', $data[2]);
                                if(!is_bool($date)){
                                    $pupil->setBirthDate($date);
                                }
                            }

                            if($data[3] != null) {
                                $date = DateTime::createFromFormat('Y', $data[3]);
                                if (!is_bool($date)) {
                                    $pupil->setExamDate($date);
                                }
                            }
                        } catch (Exception $e) {
                            $this->addFlash('error','Das Geburtsdatum muss als TT.MM.YYYY formatiert sein.');
                            return $this->redirectToRoute('app_data_import', [], Response::HTTP_SEE_OTHER);
                        }

                        $pupil->setQualificationPoints($data[4]);
                        if($teacherId) {
                            $pupil->setTeacher($teacherRepository->find($teacherId));
                        }
                        $entityManager->persist($pupil);
                        $entityManager->flush();
                    }
                    $row++;
                }
                fclose($handle);
            }
            $this->addFlash('success',($row-2).' Datensätze erfolgreich gespeichert.');
            return $this->redirectToRoute('app_pupil_crud_index', [], Response::HTTP_SEE_OTHER);

    }

}