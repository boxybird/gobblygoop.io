<?php

use App\Models\Prompt;
use Livewire\Volt\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;

new class extends Component {
    public function with(): array
    {
        $images = Prompt::inRandomOrder()
            ->limit(10)
            ->get()
            ->map(function ($prompt) {
                return $prompt->imageUrl('thumb');
            });

        return [
            'images' => $images,
        ];
    }
}; ?>

<div>
    <style>
        .squares {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 40;
            pointer-events: none;
        }

        .squares li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            background-size: cover;
            background-repeat: no-repeat;
            animation: animate 25s linear infinite;
            bottom: -150px;
        }

        @foreach ($images as $image)
            .squares li:nth-child({{ $loop->iteration }}) {
                background-image: url('{{ $image }}');
            }
        @endforeach

        .squares li:nth-child(1) {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .squares li:nth-child(2) {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .squares li:nth-child(3) {
            left: 70%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
        }

        .squares li:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .squares li:nth-child(5) {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .squares li:nth-child(6) {
            left: 75%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        .squares li:nth-child(7) {
            left: 35%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
        }

        .squares li:nth-child(8) {
            left: 50%;
            width: 25px;
            height: 25px;
            animation-delay: 15s;
            animation-duration: 45s;
        }

        .squares li:nth-child(9) {
            left: 5%;
            width: 100px;
            height: 100px;
            animation-delay: 3s;
            animation-duration: 10s;
        }

        .squares li:nth-child(10) {
            left: 85%;
            width: 150px;
            height: 150px;
            animation-delay: 0s;
            animation-duration: 11s;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }

            100% {
                transform: translateY(-100svh) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }
    </style>

    <ul class="squares">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
