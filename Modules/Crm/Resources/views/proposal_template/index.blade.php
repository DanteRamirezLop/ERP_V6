@extends('layouts.app')
@section('title', __('crm::lang.proposal_template'))
@section('content')
	@include('crm::layouts.nav') 
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
		@lang('crm::lang.proposal_template')
		<small class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold">{{__('crm::lang.crm')}}</small>
		</h1>
	</section>



	<!-- Main content -->
	<section class="content">
		@component('components.widget', ['class' => 'box-solid'])
			@if(empty($proposal_template) && auth()->user()->can('crm.add_proposal_template'))
		        @slot('tool')
		            <div class="box-tools">
		                <a class="btn btn-primary pull-right m-5" href="{{action([\Modules\Crm\Http\Controllers\ProposalTemplateController::class, 'create'])}}">
		                	<i class="fa fa-plus"></i> @lang('messages.add')
		                </a>
		            </div>
		        @endslot
	        @endif
	        @if(!empty($proposal_template))
		        <div class="row">
		        	<div class="col-md-4 col-md-offset-4">
		        		<div class="box box-info box-solid">
		        			<div class="box-body">
		        				<strong>
		        					{{$proposal_template->subject}}
		        				</strong>
		        			</div>
		        			<div class="box-footer clearfix">
		        				<div class="row">
		        					@if(auth()->user()->can('crm.add_proposal_template'))
			        					<div class="col-md-4">
			        						<a href="{{action([\Modules\Crm\Http\Controllers\ProposalTemplateController::class, 'getEdit'])}}" class="btn btn-primary pull-left">
			        							@lang('messages.edit')
			        						</a>
			        					</div>
			        				@endif
			        				@can('crm.access_proposal')
			        					<div class="col-md-4">
			        						<a href="{{action([\Modules\Crm\Http\Controllers\ProposalTemplateController::class, 'getView'])}}" class="btn btn-info">
			        							@lang('messages.view')
			        						</a>
			        					</div>
			        					<div class="col-md-4">
			        						<a href="{{action([\Modules\Crm\Http\Controllers\ProposalTemplateController::class, 'send'])}}" class="btn btn-success pull-right">
			        							@lang('crm::lang.send')
			        						</a>
			        					</div>
			        				@endcan
		        				</div>
		        			</div>
		        		</div>
		        	</div>
		        </div>
		    @else
		    	<div class="callout callout-info">
		            <h4>
		            	{{__('crm::lang.no_template_found')}}
		            </h4>
		        </div>
		    @endif
    	@endcomponent
	</section>
@endsection