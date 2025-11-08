<?php

namespace App\Enums;

enum SubjectEnum: string
{
    // ⭐ Core & Common Subjects (Grade 9–12)
    case ENGLISH = 'English';
    case MATHEMATICS = 'Mathematics';
    case PHYSICS = 'Physics';
    case CHEMISTRY = 'Chemistry';
    case BIOLOGY = 'Biology';
    case GEOGRAPHY = 'Geography';
    case HISTORY = 'History';
    case CIVIC = 'Civic & Ethical Education';
    case INFORMATION_TECHNOLOGY = 'Information Technology';
    case HPE = 'Health & Physical Education';

    // ⭐ Languages (Based on region)
    case AMHARIC = 'Amharic';
    case AFAN_OROMO = 'Afan Oromo';
    case TIGRIGNA = 'Tigrigna';
    case SOMALI = 'Somali Language';

    // ⭐ Social Science Stream (G11–12)
    case ECONOMICS = 'Economics';
    case BUSINESS = 'Business';

    // ⭐ Natural Science Stream (G11–12)
    case AGRICULTURE = 'Agriculture';

    // ⭐ Additional/Optional Subjects found in the new curriculum
    case TECHNICAL_DRAWING = 'Technical Drawing';
    case VISUAL_ARTS = 'Visual Arts';

        public static function asSelectArray(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
