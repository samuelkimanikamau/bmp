<?php

namespace App\Support;

class SmsMessageBuilder
{
    /**
     * Long, nicely formatted message (may be 2+ SMS parts).
     */
    public static function inviteLong(string $name, string $code): string
    {
        return
            "Dear {$name},\n".
            "Welcome to BPM Skyline Studio: Session 2, 16th Aug 2025, 5–7 p.m. (Peak sunset).\n".
            "Live recording as two of Nairobi’s most notable emerging DJs take the stage.\n\n".
            "More details: https://bpm.co.ke/{$code}\n".
            "Register: https://bpm.co.ke/pay/{$code}\n".
            "Can’t attend? Let us know: https://bpm.co.ke/decline/{$code}";
    }

    /**
     * Compact, 1-SMS friendly version (≤160 chars recommended).
     */
    public static function inviteShort(string $name, string $code): string
    {
        return
            "Dear {$name},.\n".
            "BPM Skyline Studio S2: 16 Aug, 5–7pm.\n".
            "Details: bpm.co.ke/{$code}\n".
            "Register: bpm.co.ke/pay/{$code}\n".
            "Decline: bpm.co.ke/decline/{$code}";
    }
}