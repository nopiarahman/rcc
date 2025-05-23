<!DOCTYPE html>
<html lang="id" x-data="{
    show: localStorage.getItem('welcomeShown') !== 'yes',
    currentSlide: 0,
    slides: @js(\App\Models\WelcomeImage::where('is_active', true)->orderBy('order')->get()->map(function($item) {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'image_url' => $item->getFirstMediaUrl('welcome_images')
        ];
    })),
    next() {
        this.currentSlide = (this.currentSlide + 1) % this.slides.length;
    },
    prev() {
        this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
    },
    masuk() {
        localStorage.setItem('welcomeShown', 'yes');
        window.location.href = '{{ route('home') }}';
    }
}" x-init="
    if (!show) {
        window.location.href = '{{ route('home') }}';
    }
    
    // Auto-advance slides every 5 seconds
    setInterval(() => {
        if (this.slides.length > 1) {
            this.next();
        }
    }, 5000);">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang - Raihaan Coffee Corner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6f42c1;
            --secondary-color: #f8f9fa;
            --text-color: #333;
            --text-muted: #6c757d;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f5f5f5;
            color: var(--text-color);
            height: 100vh;
            overflow: hidden;
        }
        
        .welcome-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        
        .welcome-slides {
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            color: white;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        
        .slide.active {
            opacity: 1;
        }
        
        .btn-enter {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 0;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-enter:hover {
            background-color: #5a32a3;
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }
        
        .slide-indicators {
            position: absolute;
            bottom: 200px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 8px;
            z-index: 10;
            padding: 8px 0;
        }
        
        .slide-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
            border: none;
            padding: 0;
            cursor: pointer;
        }
        
        .slide-dot.active {
            background-color: white;
            transform: scale(1.3);
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            .tagline {
                font-size: 1rem;
            }
            
            .btn-enter {
                padding: 0.7rem 2rem;
                font-size: 1rem;
            }
            
            .nav-arrow {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container" x-data>
        <div class="welcome-slides">
            <template x-for="(slide, index) in slides" :key="index">
                <div 
                    class="slide" 
                    :class="{ 'active': currentSlide === index }"
                    :style="'background: url(' + slide.image_url + ') no-repeat center center; background-size: cover;'">
                    {{-- <h1 x-text="slide.title" style="color: white; text-align: center; margin-top: 2rem; font-size: 2rem; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);"></h1> --}}
                </div>
            </template>
            
            <div class="slide-indicators" x-show="slides.length > 1">
                <template x-for="(slide, index) in slides" :key="index">
                    <button 
                        class="slide-dot"
                        :class="{ 'active': currentSlide === index }"
                        @click="currentSlide = index"
                        :aria-label="'Slide ' + (index + 1)">
                    </button>
                </template>
            </div>
            
            <div style="position: absolute; bottom: 60px; left: 0; right: 0; display: flex; justify-content: center; width: 100%;">
                <button 
                    class="btn-enter" 
                    @click="currentSlide < slides.length - 1 ? next() : masuk()"
                    style="min-width: 200px;"
                    x-text="currentSlide < slides.length - 1 ? 'Selanjutnya' : 'Masuk'">
                </button>
            </div>
            
            <template x-if="slides.length === 0">
                <div class="slide active" style="background: linear-gradient(135deg, #6f42c1, #4a1d96);">
                    <h1 style="color: white; text-align: center; margin-top: 2rem; font-size: 2rem;">Raihaan Coffee Corner</h1>
                    <p style="color: white; text-align: center; margin-top: 1rem;">Minuman hangat langsung ke rumahmu 🍵</p>
                    <button class="btn-enter" @click="masuk">Masuk</button>
                </div>
            </template>
        </div>
    </div>
    
    <script>
        // Add touch support for mobile swipe
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.welcome-container');
            let touchStartX = 0;
            let touchEndX = 0;
            
            function handleTouchStart(e) {
                touchStartX = e.changedTouches[0].screenX;
            }
            
            function handleTouchEnd(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }
            
            function handleSwipe() {
                const diff = touchStartX - touchEndX;
                const threshold = 50; // Minimum distance for a swipe
                
                if (diff > threshold) {
                    // Swipe left
                    Alpine.store('next')();
                } else if (diff < -threshold) {
                    // Swipe right
                    Alpine.store('prev')();
                }
            }
            
            container.addEventListener('touchstart', handleTouchStart, false);
            container.addEventListener('touchend', handleTouchEnd, false);
        });
    </script>
</body>
</html>
