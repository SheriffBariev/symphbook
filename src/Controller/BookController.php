<?php

namespace App\Controller;

use App\Entity\Book;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_list")
     * @Method({"GET"})
     */
    public function index() {
        $books = $this->getDoctrine()->getRepository(Book::class)->findAll();
//        return new Response("<html><body>Hello</body></html>");
        return $this->render('articles/index.html.twig', array('books' => $books));
    }

    /**
     * @Route("/book/delete/{id}")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id) {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($book);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }

    /**
     * @Route("/book/new", name="new_book")
     * @Method({"GET", "POST"})
     */
    public function new(Request $request) {
        $book = new Book();

        $form = $this->createFormBuilder($book)
            ->add('title', TextType::class, array(
                'attr' => array('class' => 'form-control'
                ))
            )
            ->add('year', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array('class' => 'form-control datepicker js-datepicker'),
                'format' => 'dd-mm-yyyy'
                ))
            ->add('author', TextType::class, array(
                'attr' => array('class' => 'form-control'
                ))
            )
            ->add('save', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-3'
                ))
            )
            ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $book = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($book);
                $entityManager->flush();

                return $this->redirectToRoute('book_list');
            }

        return $this->render('articles/new.html.twig', array(
            'form' => $form->createView()
        ));
     }

    /**
     * @Route("/book/edit/{id}", name="edit_book")
     * @Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $book = new Book();
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        $form = $this->createFormBuilder($book)
            ->add('title', TextType::class, array(
                    'attr' => array('class' => 'form-control'
                    ))
            )
            ->add('year', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array('class' => 'form-control datepicker js-datepicker'),
                'format' => 'dd-mm-yyyy'
            ))
            ->add('author', TextType::class, array(
                    'attr' => array('class' => 'form-control'
                    ))
            )
            ->add('save', SubmitType::class, array(
                    'label' => 'Update',
                    'attr' => array('class' => 'btn btn-primary mt-3'
                    ))
            )
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('book_list');
        }

        return $this->render('articles/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

//    /**
//     * @Route("/book/save")
//     */
//    public function save() {
//        $entityManager = $this->getDoctrine()->getManager();
//        $birthday = "1980-02-12";
//        $book = new Book();
//        $book->setTitle('Book Two');
//        $book->setYear(new \DateTime($birthday));
//        $book->setAuthor('Albert Geltz');
//
//        $entityManager->persist($book);
//
//        $entityManager->flush();
//
//        return new Response("Saved a book with the id of " .$book->getId());
//    }
}