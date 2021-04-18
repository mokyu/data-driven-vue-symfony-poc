<?php

namespace App\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetadataController extends AbstractController
{
    /**
     * Returns the metadata of the given schema
     * @Route("/api/metadata/{class}", name="schema_metadata_header", methods={"GET","HEAD"})
     * @return JsonResponse
     */
    public function index(string $class): Response
    {
        try {
            $annotations = [
                'common' => null,
                'form' => [],
                'table' => [],
            ];

            $reflectionClass = new ReflectionClass(\sprintf('App\Schema\%s', $class));
            $props = $reflectionClass->getProperties();
            $reader = new AnnotationReader();

            foreach($props as $property) {
                $propData = $reader->getPropertyAnnotations($property);
                foreach($propData as $annotation) {
                    if ($annotation instanceof \App\Annotation\Table) {
                        $annotations['table'][] = $annotation->jsonSerialize();
                    } else if ($annotation instanceof \App\Annotation\Form) {
                        $annotations['form'][] = $annotation->jsonSerialize();
                    }
                }
            }
            $classAnnotations = $reader->getClassAnnotations($reflectionClass);
            foreach($classAnnotations as $classAnnotation) {
                if ($classAnnotation instanceof \App\Annotation\Common) {
                    $annotations['common'] = $classAnnotation->jsonSerialize();
                }
            }

            return new JsonResponse($annotations);

        } catch(\ReflectionException $e) {
            return new JsonResponse(['error' => $e]);
        }
    }
}
