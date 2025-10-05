<!DOCTYPE html>
<html lang="en">
    @include('partials.header')
    <body>
    @include('partials.sidebar')
    <!-- Main Content -->
    <div class="main-content">
        @include('partials.navbar')
        @yield('content')
    </div>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('/js/main.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.3.2/af-2.7.0/b-3.2.3/b-colvis-3.2.3/cc-1.0.4/date-1.5.5/r-3.0.4/sc-2.4.3/sb-1.8.2/sp-2.3.3/sl-3.0.1/sr-1.4.1/datatables.min.js" integrity="sha384-JsOQNh594HBiVE+TLua1qeqQuaFtFz9inDw/T2CN7fAfOqQvIGWUYbCE6RGBNMES" crossorigin="anonymous"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('scripts')
    </body>
</html>
