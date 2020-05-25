@extends('rocketmail::layout.app')

@section('title', 'Templates')

@section('content-mails')


<div class="col-lg-10 col-md-12">
  
                <div class="card my-4">
                    <div class="card-header d-flex align-items-center justify-content-between"><h5>Newsletters</h5>
                        @if (!$newsletters->isEmpty() and auth()->user()->hasAnyRole('super-admin', 'admin'))
                        <a href="{{ route('backetfy.mails.selectNewsletter') }}" class="btn btn-primary">Programar newsletter</a>
                        @endif
                    </div>

                    @if ($newsletters->isEmpty())
                    
                    @component('rocketmail::layout.emptydata')
                        
                        <span class="mt-4">Ahora mismo no hay newsletters programadas</span>
                        @if (auth()->user()->hasAnyRole('super-admin', 'admin'))
                        <a class="btn btn-primary mt-3" href="{{ route('backetfy.mails.selectNewsletter') }}">Programar newsletter</a>
                        @endif

                    @endcomponent

                    @endif

                    @if (!$newsletters->isEmpty())
                    <!---->
                    <table id="newsletters_list" class="table table-responsive table-hover table-sm mb-0 penultimate-column-right">
                        <thead>
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Título</th>
                                <th scope="col"></th>

                            </tr>
                        </thead>
                        <tbody>
                        @foreach($newsletters->all() as $newsletter)
                            <tr id="newsletter_item_{{ $newsletter->id }}">
                                <td class="table-fit"><span>{{ $newsletter->send_date->format('d \d\e M \d\e Y \a \l\a\s h:m') }}</td>
                                <td class="text-left">{{ ucfirst($newsletter->title) }}</td>

                                <td class="table-fit">
                                    <a href="{{ route('backetfy.mails.viewNewsletter', [ 'newsletter_id' => $newsletter->id ]) }}" class="table-action mr-3"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 16"><path d="M16.56 13.66a8 8 0 0 1-11.32 0L.3 8.7a1 1 0 0 1 0-1.42l4.95-4.95a8 8 0 0 1 11.32 0l4.95 4.95a1 1 0 0 1 0 1.42l-4.95 4.95-.01.01zm-9.9-1.42a6 6 0 0 0 8.48 0L19.38 8l-4.24-4.24a6 6 0 0 0-8.48 0L2.4 8l4.25 4.24h.01zM10.9 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path></svg>
                                    </a>
                                    <a href="#" class="table-action remove-item" data-newsletter-slug="{{ $newsletter->newsletter_slug }}" data-newsletter-name="{{ $newsletter->newsletter_name }}">
                                        <svg enable-background="new 0 0 268.476 268.476" version="1.1" viewBox="0 0 268.476 268.476" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" class="remove">
                                            <path d="m63.119 250.25s3.999 18.222 24.583 18.222h93.072c20.583 0 24.582-18.222 24.582-18.222l18.374-178.66h-178.98l18.373 178.66zm106.92-151.81c0-4.943 4.006-8.949 8.949-8.949s8.95 4.006 8.95 8.949l-8.95 134.24c0 4.943-4.007 8.949-8.949 8.949s-8.949-4.007-8.949-8.949l8.949-134.24zm-44.746 0c0-4.943 4.007-8.949 8.949-8.949 4.943 0 8.949 4.006 8.949 8.949v134.24c0 4.943-4.006 8.949-8.949 8.949s-8.949-4.007-8.949-8.949v-134.24zm-35.797-8.95c4.943 0 8.949 4.006 8.949 8.949l8.95 134.24c0 4.943-4.007 8.949-8.95 8.949-4.942 0-8.949-4.007-8.949-8.949l-8.949-134.24c0-4.943 4.007-8.95 8.949-8.95zm128.87-53.681h-39.376v-17.912c0-13.577-4.391-17.899-17.898-17.899h-53.696c-12.389 0-17.898 6.001-17.898 17.899v17.913h-39.376c-7.914 0-14.319 6.007-14.319 13.43 0 7.424 6.405 13.431 14.319 13.431h168.24c7.914 0 14.319-6.007 14.319-13.431 0-7.423-6.405-13.431-14.319-13.431zm-57.274 0h-53.695l1e-3 -17.913h53.695v17.913z" clip-rule="evenodd" fill-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    <!---->
                </div>
            </div>

<script type="text/javascript">

    $('.remove-item').click(function(){
        var templateSlug = $(this).data('template-slug');
        var templateName = $(this).data('template-name');

        notie.confirm({

            text: '¿Seguro que quieres eliminar esta plantilla? Puede que alguna de las acciones se quede sin plantilla <br>Eliminar <b>'+ templateName +'</b>',
            submitText: 'Eliminar',
            cancelText: 'Cancelar',
            submitCallback: function () {

                axios.post('{{ route('backetfy.mails.deleteTemplate') }}', {
                    templateslug: templateSlug,
                })
                .then(function (response) {
                    if (response.data.status == 'ok'){
                        notie.alert({ type: 1, text: 'Plantilla eliminada', time: 2 });

                        jQuery('tr#template_item_' + templateSlug).fadeOut('slow');

                        var tbody = $("#templates_list tbody");

                        console.log(tbody.children().length);

                        if (tbody.children().length <= 1) {
                            location.reload();
                        }

                    } else {
                        notie.alert({ type: 'error', text: 'Plantilla no eliminada', time: 2 })
                    }
                })
                .catch(function (error) {
                    notie.alert({ type: 'error', text: error, time: 2 })
                });
            }
        })
    });      
</script>
   
@endsection