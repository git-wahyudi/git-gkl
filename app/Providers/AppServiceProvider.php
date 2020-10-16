<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Form;
use Html;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //set local timezone
        date_default_timezone_set('Asia/Jakarta');

        //===================Item isi form inputan =====================================================================
        Form::component('bsText', 'component.form.text', ['fieldname'=>'', 'label'=>'', 'value'=>null, 'required'=>false, 'class'=>'','placeholder'=>'']);
        Form::component('bsRoText', 'component.form.rotext',['fieldname','label', 'value'=>null]);
        Form::component('bsRadioInline', 'component.form.radio-inline',['fieldname','label','data'=>[], 'required'=>false, 'checked' => false]);
        Form::component('bsHidden', 'component.form.hidden',['fieldname'=>'', 'value'=>null]);
        Form::component('bsUpload', 'component.form.upload',['fieldname'=>'', 'label'=>'', 'required'=>false, 'file_type'=>'']);
        Form::component('bsSelect2', 'component.form.select2', ['fieldname', 'label', 'data'=>[], 'required'=>false, 'class'=>'']);
        Form::component('bsSelect', 'component.form.select', ['fieldname', 'label', 'data'=>[], 'required'=>false, 'class'=>'']);
        Form::component('bsPassword', 'component.form.password', ['fieldname'=>'', 'label'=>'', 'value'=>null, 'required'=>false, 'class'=>'']);
        Form::component('bsTextArea', 'component.form.textarea', ['fieldname'=>'', 'label'=>'', 'value'=>null, 'required'=>false, 'class'=>'']);
        Form::component('bsRoTextArea', 'component.form.rotextarea', ['fieldname'=>'', 'label'=>'', 'value'=>null, 'required'=>false, 'class'=>'']);
        //==============================================================================================================


        //================Templete frame body===========================================================================
        Html::component('bsHomeOpen', 'component.html.home-open', ['title'=>'']);
        Html::component('bsHomeOpenMenu', 'component.html.home-open-menu', ['title'=>'', 'link'=>'', 'text'=>'']);
        Html::component('bsHomeClose', 'component.html.home-close', []);
        //==============================================================================================================

        //================Templete modal pop up=========================================================================
        Html::component('bsModalOpen', 'component.modal.modal-open',['action'=>'','id'=>'', 'title'=>'']);
        Html::component('bsModalOpenLg', 'component.modal.modal-open-lg',['action'=>'','id'=>'', 'title'=>'']);
        Html::component('bsModalClose', 'component.modal.modal-close', ['text'=>'']);
        //==============================================================================================================

        //===================Item fill form inputan =====================================================================
        Html::component('jsClearForm','component.js.clearform',[]);
        Html::component('jsResetForm','component.js.resetform',[]);
        Html::component('jsValueForm','component.js.fill-input-form',['id'=>'','tag'=>'','name'=>'']);
        Html::component('jsValuePage','component.js.fill-input-page',['form'=>'','id'=>'','tag'=>'','name'=>'']);
        
        //===============================================================================================================


        //================javascript datatable==========================================================================
        Html::component('jsShowModal','component.js.on-show-modal', ['id'=>'']);
        Html::component('jsCloseModal','component.js.on-close-modal', []);
        Html::component('jsSubmitModal','component.js.on-submit-modal', ['id'=>'']);
        Html::component('jsAlertTrue','component.js.alert-true', []);
        Html::component('jsAlertFalse','component.js.alert-false', []);
        Html::component('jsAlertFail','component.js.alert-fail', []);
        Html::component('jsValidate','component.js.error-validate', []);
        //==============================================================================================================
    }
}
