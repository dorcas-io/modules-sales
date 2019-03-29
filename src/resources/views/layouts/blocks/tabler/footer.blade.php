<footer class="footer">
    <div class="container">
        <div class="row align-items-center flex-row-reverse">
            <div class="col-auto ml-lg-auto">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item"><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-auto">
                        <a href="" class="btn btn-outline-primary btn-sm">Support</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0 text-center">
                Copyright © {{ date('Y') }} <a href="{{ url('/') }}">{{ config('app.name') }}</a>. All rights reserved.
            </div>
        </div>
    </div>
</footer>
