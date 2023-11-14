<?php

namespace EaglenavigatorSystem\Wopi\Controller;

use App\Controller\AppController as BaseController;
use Cake\Event\Event;

class AppController extends BaseController
{

  public const HEADER_REFERENCE = 'X-WOPI-Reference';


  public function initialize()
  {
    $this->loadModel('EaglenavigatorSystem/Wopi.WopiFiles');
    $this->loadModel('EaglenavigatorSystem/Wopi.Locks');
    $this->loadModel('UserSessions');
  }


  public function beforeFilter(Event $event)
  {
    parent::beforeFilter($event);
    $this->loadComponent('RequestHandler');
  }
}
