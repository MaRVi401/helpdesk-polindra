<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Modification extends Command
{
    protected $signature = 'emang:luh-artis? {--host=127.0.0.1} {--port=8000} {--vip : Mode VIP dengan quotes inspiratif}';
    protected $description = 'Serve aplikasi (emang luh artis? 😎)';

    public function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');
        $vipMode = $this->option('vip');

        // Easter Egg: Cek jam
        $hour = date('H');
        $timeGreeting = $this->getTimeGreeting($hour);

        $this->newLine();

        // ASCII Art Logo (random)
        $this->showRandomLogo();

        $this->newLine();

        // Time-based greeting
        $this->line("  <fg=yellow>{$timeGreeting}</>");
        $this->newLine();

        // Loading animation
        $this->showLoadingAnimation();

        // Header
        $this->line('  <fg=magenta>╔═══════════════════════════════════════════════╗</>');
        $this->line('  <fg=magenta>║</> <fg=yellow;options=bold>  😎 IYALAAH GUE ARTIS.. 🎤</> <fg=magenta>                  ║</>');
        $this->line('  <fg=magenta>╚═══════════════════════════════════════════════╝</>');
        $this->newLine();


        // Random messages dengan emoji
        $messages = [
            ["🎬", "LIGHTS, CAMERA, ACTION!", "Panggung siap di"],
            ["🎸", "ROCK N' ROLL BABY!", "Server nge-rock di"],
            ["🎪", "WELCOME TO THE CIRCUS!", "Pertunjukan spektakuler di"],
            ["🎵", "DJ IN DA HOUSE!", "Musik mengalun dari"],
            ["⭐", "WALK OF FAME!", "Bintang bersinar di"],
            ["🎭", "THE SHOW MUST GO ON!", "Tirai terbuka di"],
            ["🎤", "IS THIS THING ON?", "Mic live di"],
            ["🚀", "BLAST OFF!", "Server meluncur dari"],
            ["🔥", "WE'RE ON FIRE!", "Server panas membara di"],
            ["💎", "DIAMOND TIER!", "Premium server di"],
        ];

        $selected = $messages[array_rand($messages)];

        $this->line("  <fg=cyan;options=bold>{$selected[0]} {$selected[1]}</>");
        $this->info("  {$selected[2]}");
        $this->line("  <fg=green;options=bold>► http://{$host}:{$port}</>");
        $this->newLine();

        // VIP Mode: Quotes
        if ($vipMode) {
            $this->showVipQuote();
        }

        // Tips random

        $tips = [
            "🤲 Berdo'a: Bismillah - semoga lancar..",
            "😇 Sholat-mu: Jangan lupa sholat,sebelum disholat-kan..",
        ];

        $this->comment("  " . $tips[array_rand($tips)]);
        $this->newLine();

        // Info panel
        $this->line('  <fg=blue>╭─ SERVER INFO ────────────────────────────────╮</>');
        $this->line("  <fg=blue>│</> <fg=white>Host:</> <fg=cyan>{$host}</>");
        $this->line("  <fg=blue>│</> <fg=white>Port:</> <fg=cyan>{$port}</>");
        $this->line("  <fg=blue>│</> <fg=white>PHP:</> <fg=cyan>" . PHP_VERSION . "</>");
        $this->line("  <fg=blue>│</> <fg=white>Laravel:</> <fg=cyan>" . app()->version() . "</>");
        $this->line('  <fg=blue>╰──────────────────────────────────────────────╯</>');
        $this->newLine();

        $this->line('  <fg=red;options=bold>⚠️  Tekan Ctrl+C untuk turun panggung</>');
        $this->newLine();
        $this->line('  <fg=green>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $this->newLine();

        // Sound effect (beep beep!)
        echo "\007"; // Beep sound!

        // Jalankan serve
        passthru(PHP_BINARY . " artisan serve --host={$host} --port={$port}");

        // Goodbye messages
        $this->showGoodbye();
    }

    private function getTimeGreeting($hour)
    {
        if ($hour >= 5 && $hour < 10) {
            return "☀️  Good morning, Superstar! Semangat pagi!";
        } elseif ($hour >= 10 && $hour < 15) {
            return "🌤️  Good afternoon! Lanjut coding yuk!";
        } elseif ($hour >= 15 && $hour < 18) {
            return "🌅 Good evening! Bentar lagi sunset nih!";
        } elseif ($hour >= 18 && $hour < 22) {
            return "🌙 Good night! Ngoding malam emang beda!";
        } else {
            return "🦉 Lembur ya? Jangan begadang terus, Developer!";
        }
    }

    private function showRandomLogo()
    {
        $logos = [
            [
                "  <fg=yellow>   _____ _____ _____ _____ _____ </>",
                "  <fg=yellow>  |  _  |  _  |_   _|     |   __|</>",
                "  <fg=yellow>  |     |    _| | | |-   -|__   |</>",
                "  <fg=yellow>  |__|__|_|  |  |_| |_____|_____|</>",
            ],
            [
                "  <fg=cyan>  ╔═╗╦═╗╔╦╗╦╔═╗</>",
                "  <fg=cyan>  ╠═╣╠╦╝ ║ ║╚═╗</>",
                "  <fg=cyan>  ╩ ╩╩╚═ ╩ ╩╚═╝</>",
            ],
            [
                "  <fg=magenta>  ▄▀█ █▀█ ▀█▀ █ █▀</>",
                "  <fg=magenta>  █▀█ █▀▄  █  █ ▄█</>",
            ],
        ];

        $logo = $logos[array_rand($logos)];
        foreach ($logo as $line) {
            $this->line($line);
        }
    }

    private function showLoadingAnimation()
    {
        $this->line("  <fg=cyan>Loading</>");
        $frames = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];

        for ($i = 0; $i < 20; $i++) {
            $frame = $frames[$i % count($frames)];
            echo "\r  <fg=cyan>{$frame} Preparing your stage...</>";
            usleep(50000); // 0.05 second
        }

        $this->line("\r  <fg=green>✓ Stage ready..       Sukses Ya!✅          </>");
        $this->newLine();
    }

    private function showVipQuote()
    {
        $quotes = [
            ["💎", "Code is poetry", "- Wordpress"],
            ["🌟", "First, solve the problem. Then, write the code.", "- John Johnson"],
            ["🚀", "Make it work, make it right, make it fast.", "- Kent Beck"],
            ["⚡", "Simplicity is the soul of efficiency.", "- Austin Freeman"],
            ["🎯", "Talk is cheap. Show me the code.", "- Linus Torvalds"],
            ["🔥", "The best error message is the one that never shows up.", "- Thomas Fuchs"],
        ];

        $quote = $quotes[array_rand($quotes)];

        $this->line('  <fg=yellow>╔═══ VIP QUOTE ═══════════════════════════════╗</>');
        $this->line("  <fg=yellow>║</> {$quote[0]} <fg=white>\"{$quote[1]}\"</>");
        $this->line("  <fg=yellow>║</>    <fg=gray>{$quote[2]}</>");
        $this->line('  <fg=yellow>╚═════════════════════════════════════════════╝</>');
        $this->newLine();
    }

    private function showGoodbye()
    {
        $this->newLine(2);
        $this->line('  <fg=magenta>╔═══════════════════════════════════════════════╗</>');
        $this->line('  <fg=magenta>║</> <fg=red;options=bold>           👋 CURTAIN CLOSED! 🎬</> <fg=magenta>             ║</>');
        $this->line('  <fg=magenta>╚═══════════════════════════════════════════════╝</>');
        $this->newLine();

        $goodbyes = [
            "🌟 Thanks for the show! Audience loved it!",
            "👏 Standing ovation! See you tomorrow!",
            "🎭 That's a wrap! Great performance!",
            "🎪 The circus has left the building!",
            "⭐ You were amazing tonight! Encore!",
            "🎬 Cut! That's a perfect take!",
        ];

        $this->info("  " . $goodbyes[array_rand($goodbyes)]);
        $this->comment("  💤 Server tidur dulu ya... Good night!");
        $this->newLine();

        // Final beep
        echo "\007";
    }
}