<?php

namespace App\Controller;


use App\Entity\Exam;
use App\Entity\Pupil;
use App\Form\PupilImportType;
use App\Repository\ExamRepository;
use App\Repository\ExamSubjectRepository;
use App\Repository\PupilRepository;
use App\Repository\TeacherRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ParseCsv\Csv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/data', name: 'app_data_')]
class DataImportController extends AbstractController
{

    #[Route('/import', name: 'import', methods: ['GET', 'POST'])]
    public function import(Request $request, TeacherRepository $teacherRepository, ExamSubjectRepository $examSubjectRepository): Response
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
                    try {
                        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                            if($row !== 1) {

                                for($i = 0; $i < 16; $i++){
                                    if($i == 5 or $i == 7 or $i == 9 or $i == 11 or $i == 13) {
                                       $examSubject = $examSubjectRepository->findOneBy(['abbreviation' => $data[$i]]);
                                       if($examSubject) {
                                           $fileContent[$row][] = $examSubject;
                                       } else {
                                           $fileContent[$row][] = 'FEHLER';
                                           $this->addFlash('error',sprintf("Datensatz %s enthält einen Fehler in Spalte %s",$row-1,$i+2));
                                       }

                                    } else if($i == 15 and !empty($data[$i])) {
                                        $teacher = $teacherRepository->findOneBy(['abbreviation' => $data[$i]]);
                                        if($teacher) {
                                            $fileContent[$row][] = $teacher->getLastname();
                                        } else {
                                            $fileContent[$row][] = 'FEHLER';
                                            $this->addFlash('error',sprintf("Datensatz %s enthält einen Fehler in Spalte %s",$row-1,$i+2));
                                        }
                                    } else {
                                        $fileContent[$row][] = $data[$i];
                                    }
                                }
                            }
                            $row++;
                        }
                        fclose($handle);
                        $csv = $this->getParameter('upload_directory').'/'.$csvFile->getFilename();
                    } catch (Exception $e){
                        $this->addFlash('error',$e->getMessage());
                        return $this->redirectToRoute('app_data_import', [], Response::HTTP_SEE_OTHER);
                    }

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
    public function persist(Request $request, TeacherRepository $teacherRepository, ExamSubjectRepository $examSubjectRepository, EntityManagerInterface $entityManager): Response
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
                        try {
                            $pupil->setFirstname($data[0]);
                            $pupil->setLastname($data[1]);
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
                            $this->addFlash('error',$e->getCode());
                            return $this->redirectToRoute('app_data_import', [], Response::HTTP_SEE_OTHER);
                        }

                        $pupil->setQualificationPoints($data[4]);
                        if($teacherId) {
                            $pupil->setTeacher($teacherRepository->find($teacherId));
                        } elseif (!empty($data[15])) {
                            $teacher = $teacherRepository->findOneBy(['abbreviation' => $data[15]]);
                            if($teacher) {
                                $pupil->setTeacher($teacher);
                            }
                        }
                        for($i = 1; $i <= 5; $i++) {
                            $examSubject = $examSubjectRepository->findOneBy(['abbreviation' => $data[$i*2+3]]);
                            if($examSubject) {
                                $exam = new Exam();
                                $exam->setExamNumber($i);
                                $exam->setSubject($examSubject);
                                $exam->setExamPoints($data[$i*2+4]);
                                $entityManager->persist($exam);
                                $pupil->addExam($exam);
                            }
                        }

                        $pupil->setQualificationPoints($data[4]);
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