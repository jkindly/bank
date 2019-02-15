<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 2/10/19
 * Time: 11:16 PM
 */

namespace App\Controller;


use App\Entity\Transfer;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @method User|null getUser()
 * @method Transfer|null getTransfer()
 */
abstract class BaseController extends AbstractController
{

}