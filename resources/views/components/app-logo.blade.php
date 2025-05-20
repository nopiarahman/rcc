    <div class="flex size-8 items-center justify-center rounded-md text-accent-foreground">
    {{-- <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" /> --}}
    <img src="{{asset('storage/' . $webSettings->logo_path)}}" alt="">
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-none font-semibold dark:text-white">{{ $webSettings->site_name }}</span>
</div>
