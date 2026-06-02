<div class="text-center">
    <!-- Logo -->
    <div class="flex justify-center mb-4">
        <img src="{{ asset('images/logo.svg') }}" alt="Fundlink Logo" class="h-16 w-auto object-contain">
    </div>

    <h2 class="text-3xl font-bold tracking-[-0.03em] text-gray-900 mb-1">Verifikasi OTP</h2>
    <p class="text-gray-400 font-light text-base tracking-[-0.03em] mb-1">
        Kode 6 digit telah dikirim ke
    </p>
    <p class="text-blue-600 font-semibold text-sm mb-6 truncate">{{ auth()->user()->email }}</p>

    {{-- Mail error --}}
    @if ($mailError)
        <div class="mb-4 p-3 bg-rose-50 border border-rose-100 rounded-xl text-rose-700 text-xs font-bold tracking-wider">
            ⚠️ Gagal mengirim email ke <span class="font-black">{{ auth()->user()->email }}</span>. Periksa konfigurasi SMTP atau coba kirim ulang.
        </div>
    @endif

    {{-- Resend message --}}
    @if ($resendMessage)
        <div @class([
            'mb-4 p-3 border rounded-xl text-xs font-bold uppercase tracking-wider',
            'bg-emerald-50 border-emerald-100 text-emerald-600' => str_contains($resendMessage, 'dikirim'),
            'bg-amber-50 border-amber-100 text-amber-700'       => !str_contains($resendMessage, 'dikirim'),
        ])>
            {{ $resendMessage }}
        </div>
    @endif

    <form wire:submit="verify" class="space-y-6 text-left">
        <!-- OTP 6-digit input boxes -->
        <div class="space-y-3">
            <label class="block text-center text-sm font-semibold text-gray-500 tracking-widest uppercase">
                Masukkan Kode OTP
            </label>

            <div
                x-data="{
                    d0:'', d1:'', d2:'', d3:'', d4:'', d5:'',
                    get combined(){ return this.d0+this.d1+this.d2+this.d3+this.d4+this.d5 },
                    inp(n, e){
                        const v = e.target.value.replace(/\D/g,'').slice(-1);
                        this['d'+n] = v;
                        this.$wire.set('otp', this.combined);
                        if(v && n < 5) this.$refs['i'+(n+1)].focus();
                    },
                    back(n, e){
                        if(e.key==='Backspace' && !this['d'+n] && n > 0){
                            this['d'+(n-1)] = '';
                            this.$wire.set('otp', this.combined);
                            this.$refs['i'+(n-1)].focus();
                        }
                    },
                    paste(e){
                        const t = (e.clipboardData||window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
                        ['d0','d1','d2','d3','d4','d5'].forEach((k,i)=>{ this[k] = t[i]||'' });
                        this.$wire.set('otp', this.combined);
                        const last = Math.min(t.length, 5);
                        this.$refs['i'+last].focus();
                        e.preventDefault();
                    }
                }"
                class="flex justify-center gap-2 sm:gap-3"
                @paste.window="paste($event)"
            >
                @foreach(range(0,5) as $i)
                <input
                    x-ref="i{{ $i }}"
                    type="text"
                    inputmode="numeric"
                    maxlength="1"
                    :value="d{{ $i }}"
                    @input="inp({{ $i }}, $event)"
                    @keydown="back({{ $i }}, $event)"
                    @focus="$event.target.select()"
                    autocomplete="one-time-code"
                    class="w-11 h-14 sm:w-12 sm:h-14 text-center text-2xl font-black text-gray-900 bg-white border-2 border-gray-200 rounded-2xl focus:border-blue-600 focus:ring-0 transition-all duration-150 shadow-sm"
                >
                @endforeach
            </div>

            <x-input-error :messages="$errors->get('otp')" class="mt-1 text-center" />
        </div>

        <!-- Verify Button -->
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="verify"
            class="w-full h-[46px] flex items-center justify-center gap-2 rounded-xl bg-blue-600 text-white shadow-xl shadow-blue-100 hover:bg-blue-700 active:scale-[0.98] transition-all duration-200 font-bold text-sm tracking-wide disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="verify">Verifikasi Akun</span>
            <span wire:loading wire:target="verify" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Memverifikasi...
            </span>
        </button>
    </form>

    <!-- Resend Section with countdown -->
    <div
        x-data="{
            countdown: {{ $resendCooldown }},
            timer: null,
            start(){
                clearInterval(this.timer);
                if(this.countdown > 0){
                    this.timer = setInterval(()=>{
                        if(this.countdown > 0) this.countdown--;
                        else clearInterval(this.timer);
                    }, 1000);
                }
            }
        }"
        x-init="start()"
        x-on:resend-started.window="countdown = {{ $resendCooldown }}; start()"
        class="mt-6 text-center"
    >
        <p class="text-sm text-gray-400 font-light mb-1">Tidak menerima kode?</p>

        <template x-if="countdown > 0">
            <p class="text-sm text-gray-400">
                Kirim ulang dalam
                <span class="font-bold text-blue-600" x-text="countdown + ' detik'"></span>
            </p>
        </template>

        <template x-if="countdown === 0">
            <button
                wire:click="resend"
                wire:loading.attr="disabled"
                wire:target="resend"
                @click="countdown = 60; start()"
                class="text-blue-600 font-semibold text-sm hover:underline disabled:opacity-40 disabled:cursor-not-allowed transition-opacity"
            >
                <span wire:loading.remove wire:target="resend">Kirim Ulang OTP</span>
                <span wire:loading wire:target="resend">Mengirim...</span>
            </button>
        </template>
    </div>

    <!-- Divider & logout -->
    <div class="mt-8 pt-6 border-t border-gray-100">
        <button
            wire:click="logout"
            class="text-xs text-gray-400 hover:text-gray-600 transition-colors"
        >
            Masuk dengan akun lain
        </button>
    </div>
</div>
