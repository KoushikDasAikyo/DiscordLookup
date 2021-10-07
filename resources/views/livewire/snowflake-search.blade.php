<div id="snowflake">

    <h1 class="mb-4 mt-5 text-center text-white">{{ __('Snowflake') }}</h1>
    <div class="mt-2 mb-4">
        <div class="row">

            <div class="col-12 col-lg-6 offset-lg-3">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-dark">
                        <i class="far fa-snowflake"></i>
                    </span>
                    <input wire:model="snowflake" id="snowflakeInput" onkeyup="updateSnowflake(this.value);" class="form-control form-control-lg" type="text" placeholder="{{ __('Snowflake') }}">
                </div>
                <small>
                    <a href="{{ route('help') }}#what-is-a-snowflake-and-how-do-i-find-one" target="_blank" class="text-muted text-decoration-none">
                        <i class="far fa-question-circle"></i> <i>{{ __('What is a Snowflake and how do I find one?') }}</i>
                    </a>
                </small>
            </div>

            <div wire:ignore id="invalidSnowflake" class="col-12 col-lg-6 offset-lg-3 mt-3" style="display: none;">
                <div id="invalidSnowflakeMessage" class="alert alert-danger fade show" role="alert"></div>
            </div>

            <div wire:ignore id="validSnowflake" class="col-12 col-lg-6 offset-lg-3 mt-3" style="display: none;">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <b>{{ __('Date') }}:</b> <span id="snowflakeDate"></span><br>
                        <b>{{ __('Relative') }}:</b> <span id="snowflakeRelative"></span><br>
                        <b>{{ __('Unix Timestamp') }}:</b> <span id="snowflakeUnix"></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 offset-lg-3 mb-3 mt-3">
                <hr>
                <button wire:click="fetchSnowflake" onclick="hideSections();" type="submit" class="btn btn-primary w-100 mt-3">{{ __('Fetch Discord Information') }}</button>
            </div>

            <div id="displaysectionLoading" class="d-flex justify-content-center" style="display: none !important;">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">{{ __('Loading...') }}</span>
                </div>
            </div>

            @if($rateLimitHit)
                <div id="displaysectionRatelimit" class="col-12 col-lg-6 offset-lg-3">
                    <div class="alert alert-danger fade show" role="alert">
                        {{ __('You send too many requests!') }}
                        @auth
                            Please try again in {{ $rateLimitAvailableIn }} seconds.
                        @endauth
                        @guest
                            Please try again in {{ $rateLimitAvailableIn }} seconds or log in with your Discord account to increase the limit.
                        @endguest
                    </div>
                </div>
            @endif

            @if($template == "notfound")
                <div id="displaysectionNotfound" class="col-12 col-lg-6 offset-lg-3">
                    <div class="alert alert-danger fade show" role="alert">
                        {{ __('No Discord user or guild could be found for the entered Snowflake.') }}<br>
                        {{ __('It is possible that the entered guild has disabled the server widget and discovery.') }}<br>
                        <br>
                        <a href="{{ route('help') }}#why-some-guilds-cant-be-found-by-their-id" target="_blank" class="text-black text-decoration-none">
                            <i class="far fa-question-circle"></i> <i>{{ __('Why some guilds can\'t be found by their ID/Snowflake?') }}</i>
                        </a>
                    </div>
                </div>
            @endif

            <div id="displaysectionContent" class="col-12 col-lg-6 offset-lg-3" @if(!$found)style="display: none;"@endif>
                @if($template == "user")
                    <div class="card text-white bg-dark">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-auto me-auto ms-auto me-lg-0 ms-lg-0">
                                    <a href="{{ $userAvatarUrl }}" target="_blank">
                                        <img class="rounded-circle" src="{{ $userAvatarUrl }}" style="width: 64px; height: 64px;" width="64px" height="64px" alt="user avatar">
                                    </a>
                                </div>
                                <div class="col-auto me-auto ms-auto me-lg-0 ms-lg-0 text-center text-lg-start align-self-center">
                                    <b>{{ $userUsername }}<small class="text-muted">#{{ $userDiscriminator }}</small></b>
                                    @if($userIsBot)
                                        <span class="badge" style="color: #fff; background-color: #5865f2; top: -1px; position: relative;">
                                        @if($userIsVerifiedBot)
                                                <i class="fas fa-check"></i>&nbsp;
                                            @endif
                                            {{ __('BOT') }}
                                    </span>
                                    @endif
                                    <div class="small text-muted">{{ $userId }}</div>
                                </div>
                                @if($userBannerUrl)
                                    <div class="col-auto me-auto ms-auto me-lg-0 mt-3 mt-sm-0">
                                        <a href="{{ $userBannerUrl }}" target="_blank">
                                            <img src="{{ $userBannerUrl }}" class="rounded-3" style="height: 64px;" height="64px" alt="user banner">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($userAboutMe)
                                {{ $userAboutMe }}
                                <hr>
                            @endif
                            <div>
                                <b>{{ __('Account Created') }}:</b> <span wire:ignore id="accountCreated"></span><br>
                                <b>{{ __('Bot') }}:</b> @if($userIsBot) &#10004; @else &#10060; @endif <br>
                                @if($userIsBot)
                                    <b>{{ __('Verified Bot') }}:</b> @if($userIsVerifiedBot) &#10004; @else &#10060; @endif <br>
                                @endif

                                @if($userBannerColor)
                                    <b>{{ __('Banner Color') }}:</b> <span style="background-color: {{ $userBannerColor }};">{{ $userBannerColor }}</span><br>
                                @endif

                                @if($userAccentColor)
                                    <b>{{ __('Accent Color') }}:</b> <span style="background-color: {{ $userAccentColor }};">{{ $userAccentColor }}</span><br>
                                @endif

                                @if(!empty($userFlagList))
                                    <b>{{ __('Badges') }}:</b>
                                    <ul style="list-style-type: none;">
                                        @foreach($userFlagList as $flag)
                                            <li style="margin-left: -1rem;">
                                                <img src="{{ $flag['image'] }}" style="max-height: 16px; max-width: 16px;" alt="{{ $flag['name'] }} badge icon"> {{ $flag['name'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                                {{-- TODO: top.gg API fetch for bots? --}}
                            </div>
                        </div>
                    </div>
                @elseif($template == "guild")
                    <div class="card text-white bg-dark" @if(empty($guildName))style="display: none;"@endif>
                        <div class="card-header">
                            <div class="row">
                                <div class="col-auto me-auto ms-auto me-lg-0 ms-lg-0">
                                    <a href="{{ $guildIconUrl }}" target="_blank">
                                        <img class="rounded-circle" src="{{ $guildIconUrl }}" style="width: 64px; height: 64px;" width="64px" height="64px" alt="guild icon">
                                    </a>
                                </div>
                                <div class="col-auto me-auto ms-auto me-lg-0 ms-lg-0 mt-3 mt-sm-0 text-center text-lg-start align-self-center">
                                    <b>{{ $guildName }}</b>
                                    @if($guildIsPartnered)<img src="{{ asset('images/discord/icons/server/partner.png') }}" class="discord-badge" alt="discord partner badge">@endif
                                    @if($guildIsVerified)<img src="{{ asset('images/discord/icons/server/verified.png') }}" class="discord-badge" alt="discord verified badge">@endif
                                    <div class="small">
                                        @if($guildOnlineCount != null)
                                            <span class="discord-status-pill discord-status-pill-online"></span> {{ number_format($guildOnlineCount, 0, '', '.') }} Online
                                        @endif
                                        @if($guildMemberCount != null)
                                            <span class="discord-status-pill discord-status-pill-offline ms-3"></span> {{ number_format($guildMemberCount, 0, '', '.') }} Members<br>
                                        @endif
                                    </div>
                                </div>
                                @if($guildBannerUrl)
                                    <div class="col-auto me-auto ms-auto me-lg-0 mt-3 mt-sm-0">
                                        <a href="{{ $guildBannerUrl }}" target="_blank">
                                            <img src="{{ $guildBannerUrl }}" class="rounded-3" style="height: 64px;" height="64px" alt="guild banner">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($guildDescription)
                                {{ $guildDescription }}
                                <hr>
                            @endif
                                <div>
                                    @if($inviteChannelId)
                                        <b>{{ __('Invite Channel') }}:</b> <a href="https://discord.com/channels/{{ $guildId }}/{{ $inviteChannelId }}" target="_blank" class="text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $inviteChannelId }}">{{ $inviteChannelName }}</a><br>
                                        <br>
                                    @endif
                                    @if($guildInstantInvite)
                                        <b>{{ __('Guild Invite URL') }}:</b> <a href="https://discord.gg/{{ $guildInstantInvite }}" target="_blank" class="text-decoration-none">https://discord.gg/{{ $guildInstantInvite }}</a><br>
                                    @endif
                                    @if($guildVanityUrlCode)
                                        <b>{{ __('Guild Vanity URL') }}:</b> <a href="{{ $guildVanityUrl }}" target="_blank" class="text-decoration-none">{{ $guildVanityUrl }}</a><br>
                                    @endif
                                    @if($guildIsNSFW != null)
                                        <b>{{ __('Guild NSFW') }}:</b> @if($guildIsNSFW) &#10004; @else &#10060; @endif <br>
                                        @if($guildIsNSFW)
                                            <b>{{ __('Guild NSFW Level') }}:</b> {{ $guildIsNSFWLevel }}<br>
                                        @endif
                                    @endif
                                    @if(!empty($guildFeatures))
                                        <b>{{ __('Guild Features') }}:</b>
                                        <ul style="text-transform: capitalize;">
                                            @foreach($guildFeatures as $feature)
                                                <li>{{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @if($snowflake)
        <script>
            document.addEventListener('livewire:load', function () {
                updateSnowflake("{{ $snowflake }}");
                Livewire.emit('fetchSnowflake');
                hideSections();
            })
        </script>
    @endif

    @push('scripts')
        <script>

            window.addEventListener('contentChanged', event => {
                var accountCreatedSpan = document.getElementById('accountCreated');
                if(accountCreatedSpan != null) {
                    accountCreatedSpan.innerText = validateSnowflake(event.detail.snowflake);
                }

                {{-- TODO: Remove jQuery --}}
                if(event.detail.invitecode !== "") {
                    $.ajax({
                        type: 'GET',
                        url: 'https://discord.com/api/v9/invites/' + event.detail.invitecode + '?with_counts=true&with_expiration=false',
                        success: function (respond) {
                            Livewire.emit('parseInviteJson', respond);
                        }
                    });
                }
            });

            function hideSections() {
                $('#displaysectionLoading').show();
                $('#displaysectionRatelimit').hide();
                $('#displaysectionNotfound').hide();
                $('#displaysectionContent').hide();
            }

            function updateSnowflake(value) {
                if(value.length > 0) {
                    var date = validateSnowflake(value);

                    if (date.toString().startsWith("That")) {
                        $('#validSnowflake').hide();
                        $('#invalidSnowflake').show();
                        document.getElementById('invalidSnowflakeMessage').innerText = date;
                    } else {
                        $('#invalidSnowflake').hide();
                        $('#validSnowflake').show();
                        document.getElementById('snowflakeDate').innerText = date;
                        document.getElementById('snowflakeRelative').innerText = moment.utc(date).local().fromNow();
                        document.getElementById('snowflakeUnix').innerText = date.getTime();
                    }
                }else{
                    $('#validSnowflake').hide();
                    $('#invalidSnowflake').hide();
                }
            }
        </script>
    @endpush

</div>
