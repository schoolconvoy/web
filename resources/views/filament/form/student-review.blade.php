<div>
    @if(isset($review['bio']))
        <div class="grid grid-cols-2 grid-flow-col">
            <div class="flex flex-col gap-2">
                <div>
                    <small>Firstname</small>
                    <h2>
                        {{ $review['bio']['firstname'] }}
                    </h2>
                </div>
                <div>
                    <small>Gender</small>
                    <h2>
                        {{ $review['bio']['gender'] }}
                    </h2>
                </div>
                <div>
                    <small>Phone</small>
                    <h2>
                        {{ $review['bio']['phone'] }}
                    </h2>
                </div>
                <div>
                    <small>Height</small>
                    <h2>
                        {{ $review['bio']['height'] }}
                    </h2>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <div>
                    <small>Lastname</small>
                    <h2>
                        {{ $review['bio']['lastname'] }}
                    </h2>
                </div>
                <div>
                    <small>Email</small>
                    <h2>
                        {{ $review['bio']['email'] }}
                    </h2>
                </div>
                <div>
                    <small>Weight</small>
                    <h2>
                        {{ $review['bio']['weight'] }}
                    </h2>
                </div>
            </div>
        </div>
    @endif
</div>

