<?php

namespace Istorm\Bundle\AdminExtBundle\Controller;

use Istorm\ShopBundle\Entity\Item;
use Istorm\ShopBundle\Entity\ItemGallery;
use Istorm\ShopBundle\Form\ItemType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as EasyAdminController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends EasyAdminController
{
    /**
     * @Route("/", name="easyadmin")
     * @Route("/", name="admin")
     *
     * The 'admin' route is deprecated since version 1.8.0 and it will be removed in 2.0.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

    protected function preUpdateEntity($entity)
    {
        if(method_exists($entity, 'upload')
        ){
            $entity->upload();

        }

        return $entity;
    }

    protected function prePersistEntity($entity)
    {
        if(method_exists($entity, 'upload')
        ){
            $entity->upload();
        }
        return $entity;
    }

    public function createItemNewForm($entity, $fields){

        $form=$this->createForm(new ItemType(), $entity, array(
            'method' => 'POST',
            'data_class' => get_class($entity),
            'attr' => array('id' => 'new-form'),
        ));
        $form->remove('gallery');

        return $form;

    }
    public function createItemEditForm($entity, $fields){

        $form=$this->createForm(new ItemType(), $entity, array(
            'method' => 'POST',
            'data_class' => get_class($entity),
            'attr' => array('id' => 'new-form'),
        ));

        return $form;

    }

    public function prePersistItemEntity($entity){

        //$entity->upload();

        $em=$this->getDoctrine()->getManager();

        $em->persist($entity);

        /**
         * @var $request Request
         */
        $request=$this->get('request');

        $files=$request->files->get('form');

        $images=$files['images'];

        foreach($images as $image){

            if(!($image instanceof UploadedFile)){
                continue;
            }

            $gallery_image=new ItemGallery();

            $gallery_image->setFile($image);
            $gallery_image->setItem($entity);

            $gallery_image->upload();

            $em->persist($gallery_image);

        }

        $em->flush();

    }

    public function preUpdateItemEntity($entity)
    {

        //$entity->upload();

        $em = $this->getDoctrine()->getManager();

        $em->persist($entity);

        $em->flush();

        /**
         * @var $request Request
         */
        $request = $this->get('request');

        $files = $request->files->get('form');

        $images = isset($files['images']) ? $files['images']: array();

        foreach ($images as $image) {

            if (get_class($image) != 'Symfony\Component\HttpFoundation\File\UploadedFile'){
                continue;
            }

            $gallery_image = new ItemGallery();

            $gallery_image->setFile($image);
            $gallery_image->setItem($entity);

            $gallery_image->upload();

            $em->persist($gallery_image);

        }

        $em->flush();

    }

    public function reorderGalleryAction(){

        $request=$this->container->get('request');

        $new_order=trim($request->request->get('new_order'));

        $em=$this->container->get('doctrine')->getManager();

        $repository=$em->getRepository('IstormShopBundle:ItemGallery');

        $cnt=0;
        foreach(explode(" ",$new_order) as $item_id){
            $item=$repository->find($item_id);
            if($item){
                $item->setPosition($cnt);
                $em->persist($item);
                $cnt++;
            }
        }
        $em->flush();

        return new Response('ok');

    }

    public function deleteGalleryImageAction(){

        $request=$this->request;

        $image_id=$request->query->get('image_id');

        $item_id=$request->query->get('item_id');

        $entity_name=$request->query->get('entity');

        $entity_full_name=$this->config['entities'][$entity_name]['class'];

        $entity = $this->em->getRepository($entity_full_name)->find($item_id);

        $em=$this->getDoctrine()->getManager();

        $image=$em->getRepository('IstormShopBundle:ItemGallery')->find($image_id);

        if($image){
            $em->remove($image);
            $em->flush();
        }

        return $this->redirectToRoute('admin', array(
            'action' => 'edit',
            'entity' => $entity_name,
            'id'=>$entity->getId(),
        ));

    }

    public function removeAction(){

        $request=$this->request;

        $id=$request->query->get('id');

        $entity_name=$request->query->get('entity');

        $entity_full_name=$this->config['entities'][$entity_name]['class'];

        $entity = $this->em->getRepository($entity_full_name)->find($id);

        $this->em->remove($entity);

        $this->em->flush();

        return $this->redirectToRoute('admin', array(
            'view' => 'list',
            'entity' => $entity_name,
        ));

    }

    public function copyAction(){

        $request=$this->request;

        $id=$request->query->get('id');

        $entity_name=$request->query->get('entity');

        $entity_full_name=$this->config['entities'][$entity_name]['class'];

        $entity = $this->em->getRepository($entity_full_name)->find($id);

        $entity_new=clone $entity;

        $this->em->persist($entity_new);

        $this->em->flush();

        return $this->redirectToRoute('admin', array(
            'action' => 'edit',
            'entity' => $entity_name,
            'id' => $entity_new->getId(),
        ));

    }
}