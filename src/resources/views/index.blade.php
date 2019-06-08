@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9" id="service-requests-main">

        You can view details of incoming <strong>Requests</strong> for your professional service here:
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#new_requests">New Requests</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#processed_requests">Processed Requests</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane container active" id="new_requests">
                <br/>
                <div class="row" class="col-md-12">
					<professional-service-request class="m6" v-for="(request, index) in newRequests" :key="request.id" :index="index" :request="request" v-on:request-marked="marked">
					</professional-service-request>
					<ul class="row col-md-12 pagination justify-content-end" v-if="newRequests.length > 0 && typeof newMeta.pagination !== 'undefined' && newMeta.pagination.total_pages >= 1">
						<li class="page-item" :class="{ 'disabled'  : page_number===1 }"><a class="page-link" href="#!" v-on:click.prevent="changePage(1,'new')">First</a></li>
						<li class="page-item" v-for="n in newMeta.pagination.total_pages" v-bind:class="{active: n === page_number}">
						    <a class="page-link" href="#" v-on:click.prevent="changePage(n,'new')" v-if="n !== page_number">@{{ n }}</a>
						    <a class="page-link" href="#" v-else>@{{ n }}</a>
						</li>
						<li class="page-item" :class="{ 'disabled'  : page_number===newMeta.pagination.total_pages }"><a class="page-link" href="#!" v-on:click.prevent="changePage(newMeta.pagination.total_pages,'new')">Last</a></li>
					</ul>

		            <div class="col-md-12" v-if="newRequests.length === 0 && !is_processing">
		                @component('layouts.blocks.tabler.empty-fullpage')
		                    @slot('title')
		                        No New Service Requests
		                    @endslot
		                    There are no <strong>new</strong> Service Requests in your inbox at the moment.
		                    @slot('buttons')
		                        
		                    @endslot
		                @endcomponent
		            </div>
		        </div>
                <div class="row col-md-12" v-if="newRequests.length === 0 && is_processing">
                	<div class="loader"></div>
                	<div>Loading new Requests</div>
                </div>
            </div>
            <div class="tab-pane container" id="processed_requests">
                <br/>
                <div class="row col-md-12">
					<professional-service-request class="m6" v-for="(request, index) in processedRequests" :key="requests.id" :index="index" :request="request" v-on:request-marked="marked">
					</professional-service-request>
					<ul class="row col-md-12 pagination justify-content-end" v-if="processedRequests.length > 0 && typeof meta.pagination !== 'undefined' && meta.pagination.total_pages >= 1">
						<li class="page-item" :class="{ 'disabled'  : page_number2===1 }"><a class="page-link" href="#!" v-on:click.prevent="changePage(1,'processed')">First</a></li>
						<li class="page-item" v-for="n in meta.pagination.total_pages" v-bind:class="{active: n === page_number2}">
						    <a class="page-link" href="#" v-on:click.prevent="changePage(n,'processed')" v-if="n !== page_number2">@{{ n }}</a>
						    <a class="page-link" href="#" v-else>@{{ n }}</a>
						</li>
						<li class="page-item" :class="{ 'disabled'  : page_number2===meta.pagination.total_pages }"><a class="page-link" href="#!" v-on:click.prevent="changePage(meta.pagination.total_pages,'processed')">Last</a></li>
					</ul>

		            <div class="col-md-12" v-if="processedRequests.length === 0 && !is_processing">
		                @component('layouts.blocks.tabler.empty-fullpage')
		                    @slot('title')
		                        No Processed Service Requests
		                    @endslot
		                    There are no <strong>processed</strong> Service Requests in your inbox at the moment.
		                    @slot('buttons')
		                        
		                    @endslot
		                @endcomponent
		            </div>
		        </div>
                <div class="row" class="col-md-12" v-if="processedRequests.length === 0 && is_processing">
                	<div class="loader"></div>
                	<div>Loading processed Requests</div>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection
@section('body_js')
    <script type="text/javascript">
        new Vue({
            el: '#service-requests-main',
            data: {
                requests: [],
                meta: [],
                page_number: 1,
                is_processing: false,
                page_number2: 1,
                is_processing2: false
            },
            mounted: function () {
                this.loadRequests();
                //console.log(this.meta)
                //console.log(this.processedMeta)
            },
            computed: {
            	newRequests: function() {
            		return this.requests.filter(function(u) {
            			return u.status==="pending";
            		});
            	},
            	processedRequests: function() {
            		return this.requests.filter(function(u) {
            			return u.status==="accepted" || u.status==="rejected";
            		});
            	},
            	newMeta: function() {
            		if (typeof this.meta.pagination !== 'undefined') {
	            		let old = this.meta.pagination;
	            		return {
	            			pagination: {
	            				count: old.per_page,
	            				current_page: this.page_number,
	            				per_page: old.per_page,
	            				total: this.newRequests.length,
	            				total_pages: Math.ceil(this.newRequests.length/old.per_page)
	            			}
	            		}
            		} else {
            			return false
            		}

            	},
            	processedMeta: function() {
            		if (typeof this.meta.pagination !== 'undefined') {
	            		let old = this.meta.pagination;
	            		return {
	            			pagination: {
	            				count: old.per_page2,
	            				current_page: this.page_number2,
	            				per_page: old.per_page,
	            				total: this.processedRequests.length,
	            				total_pages: Math.ceil(this.processedRequests.length/old.per_page)
	            			}
	            		};
	            	} else {
            			return false
            		}
            	}
            },
            methods: {
                changePage: function (number,tab) {
                	if (tab=='processed')  {
                		this.page_number2 = parseInt(number, 10);
                		this.loadRequests('processed');
                	} else {
                		this.page_number2 = parseInt(number, 10);
                		this.loadRequests('new');
                	}
                },
                loadRequests: function (tab) {
                    var context = this;
                    this.is_processing = true;
                    this.requests = [];
                    axios.get("/mps/service-requests", {
                        params: {
                            limit: 12,
                            page: tab === 'new' ? context.page_number : context.page_number2
                        }
                    }).then(function (response) {
                    	console.log(response.data)
                        context.is_processing = false;
                        context.is_processing2 = false;
                        context.requests = response.data.data;
                        context.requests2 = response.data.data;
                        context.meta = response.data.meta;
                        context.meta2 = response.data.meta;
                    }).catch(function (error) {
                        var message = '';
                        context.is_processing = false;
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
                            var e = error.response;
                            message = e.data.message;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        return swal("Oops!", message, "warning");
                    });
                },
                marked: function (index, request) {
                    this.requests.splice(index, 1, request);
                }
            }
        });
    </script>
@endsection