@php
    $container =  substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 15)), 0, 15);
@endphp


@if(!app()->bound('acaptcha_assets'))
    <link rel="stylesheet" type="text/css" href="/vendor/acaptcha/assets/css/dragCaptcha.css">
@endif

<script type="module">
    // Flag to track if Drag and Utils have been imported
    let isModulesLoaded = false;

    async function loadModules() {
        if (!isModulesLoaded) {
            const { Drag, Utils } = await import("/vendor/acaptcha/assets/js/dragCaptcha.js");
            isModulesLoaded = true; // Set the flag to true after loading
            return { Drag, Utils };
        }
        return null; // Return null if already loaded
    }

    async function initCaptcha() {
        const { Drag, Utils } = await loadModules();

        if (Drag && Utils) {
            let container = document.querySelector('.{{$container}}');
            let drag = new Drag(container,'{{config('acaptcha.language')}}');

            let checkbox = container.querySelector(".aCaptchaShow");

            // Implement the match callback method
            drag.matchFunc = function ($mask) {
                // Request verify
                Utils.request('POST', '/acaptcha/verify', JSON.stringify({'_token': '{{csrf_token()}}', 'mask': $mask}), Utils.bind(this, function(response) {
                    if (response.hash !== 0) {
                        this.matchSuccess();
                        container.querySelector('.a_captcha_hash_salt').value = response.hash;

                        if ('game_end' in response && response.game_end) {
                            checkbox.checked=true;
                            checkbox.setAttribute('data-disabled',true);
                            setTimeout(() => drag.destroy(), 1000);
                        } else {
                            setTimeout(() => checkbox.dispatchEvent(new Event('click')), 1000);
                        }
                    } else {
                        this.matchFail();
                        setTimeout(() => checkbox.dispatchEvent(new Event('click')), 1000);
                    }
                }, {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                }), Utils.bind(this, function(response) {
                    this.matchFail();
                    setTimeout(() => checkbox.dispatchEvent(new Event('click')), 1000);
                }));
            }

            function aCaptchaShow(evt) {
                if(checkbox.getAttribute('data-disabled'))
                {
                    this.checked=true;
                    return;
                }
                this.checked = false;
                // Request rendering data
                Utils.request('GET', '/acaptcha/generate', null, function(response) {
                    if ('game_end' in response && response.game_end){
                        container.querySelector('.a_captcha_hash_salt').value = response.hash;
                        checkbox.checked=true;
                        checkbox.setAttribute('data-disabled',true);
                    }else{
                        drag.render(response);
                    }

                }, function(error) {
                    console.log(error);
                });
            }

            checkbox.addEventListener('click', aCaptchaShow);


        }
    }

    initCaptcha();
</script>

@php
    app()->bind('acaptcha_assets', '1');
@endphp





<div class="captcha-box {{$container}}" >
    <div class="captcha-checkbox">
        <input type="hidden" class="a_captcha_hash_salt" name="a_captcha_hash_salt" />
        <input type="checkbox" class="aCaptchaShow" required />
        <label for="aCaptchaShow">{{ __('ACaptcha::acaptcha.i_am_not_robot',[],config('acaptcha.language')) }}</label>
    </div>
</div>
