<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
namespace fecshop\app\appfront\modules\Customer\controllers;
use Yii;
use fec\helpers\CModule;
use fec\helpers\CRequest;
use fecshop\app\appfront\modules\AppfrontController;
use fecshop\app\appfront\helper\test\My;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class AccountController extends AppfrontController
{
    //protected $_registerSuccessRedirectUrlKey = 'customer/account';
	
	public function init(){
		parent::init();
	}
	/**
	 * 账户中心
	 */
	public function actionIndex(){
		if(Yii::$app->user->isGuest){
			Yii::$service->url->redirectByUrlKey('customer/account/login');
		}
		
		$data = $this->getBlock()->getLastData();
		
		return $this->render($this->action->id,$data);
	}
	/**
	 * 登录
	 */
    public function actionLogin()
    {
		
		/**
		$toEmail = 'zqy234@126.com';
		// \fecshop\app\appfront\modules\Mailer\Email::sendLoginEmail($toEmail);
		\fecshop\app\appfront\modules\Mailer\Email::sendRegisterEmail($toEmail);
		
		exit;
		*/
		$param = Yii::$app->request->post('editForm');
		$this->getBlock()->login($param);
		$data = $this->getBlock()->getLastData();
		return $this->render($this->action->id,$data);
	}
	/**
	 * 注册
	 */
	public function actionRegister()
    {
		$param = Yii::$app->request->post('editForm');
		if(!empty($param)){
			$registerStatus = $this->getBlock()->register($param);
			//echo $registerStatus;exit;
			if($registerStatus){
				$params_register = Yii::$app->getModule('customer')->params['register'];
				# 注册成功后，是否自动登录
				if(isset($params_register['successAutoLogin']) && $params_register['successAutoLogin']  ){
					Yii::$service->customer->login($param);
				}
				# 注册成功后，跳转的页面，如果值为false， 则不跳转。
				if(isset($params_register['loginSuccessRedirectUrlKey']) && $params_register['loginSuccessRedirectUrlKey']  ){
					$redirectUrl = Yii::$service->url->getUrl($params_register['loginSuccessRedirectUrlKey']);
					Yii::$service->url->redirect($redirectUrl);
				}
			}
		}
		$data = $this->getBlock()->getLastData($param);
		return $this->render($this->action->id,$data);
	}
	
	/**
	 * 登出账户
	 */
	public function actionLogout(){
		$rt = Yii::$app->request->get('rt');
		if(!Yii::$app->user->isGuest){
			Yii::$app->user->logout();
		}
		if($rt){
			$redirectUrl = base64_decode($rt);
			//exit;
			Yii::$service->url->redirect($redirectUrl);
		}else{
			Yii::$service->url->redirect(Yii::$service->url->HomeUrl());
		}
	}
	/**
	 * ajax 请求 ，得到是否登录账户的信息
	 */
	public function actionLogininfo(){
		if(!Yii::$app->user->isGuest){
			echo json_encode([
				'loginStatus' => true,
			]);
			exit;
		}
	}
	/**
	 * 忘记密码？
	 */
	public function actionForgotpassword(){
		$data = $this->getBlock()->getLastData();
		return $this->render($this->action->id,$data);
	
	}
	
	public function actionForgotpasswordsubmit(){
		$editForm = Yii::$app->request->post('editForm');
		$data = [
			'forgotPasswordUrl' => Yii::$service->url->getUrl('customer/account/forgotpassword'),
			'contactUrl'		=> Yii::$service->url->getUrl('contacts/index/index'),
		];
		if(!empty($editForm)){
			$identity = $this->getBlock('forgotpassword')->sendForgotPasswordMailer($editForm);
			//var_dump($identity);
			if($identity){
				$data['identity'] =  $identity;
			}else{
				$redirectUrl = Yii::$service->url->getUrl('customer/account/forgotpassword');
				Yii::$service->url->redirect($redirectUrl);
			}
		}
		return $this->render($this->action->id,$data);
	}
	
	
	
	public function actionResetpassword(){
		$editForm = Yii::$app->request->post('editForm');
		if(!empty($editForm)){
			$resetStatus = $this->getBlock()->resetPassword($editForm);
			if($resetStatus){
				# 重置成功，跳转
				$resetSuccessUrl = Yii::$service->url->getUrl('customer/account/resetpasswordsuccess');
				Yii::$service->url->redirect($resetSuccessUrl);
			}
		}
		$data = $this->getBlock()->getLastData();
		return $this->render($this->action->id,$data);
		
	}
	
	public function actionResetpasswordsuccess(){
		$data = $this->getBlock()->getLastData();
		return $this->render($this->action->id,$data);
	}
	
}
















