<?php

namespace Filament\Forms\Components;

use Carbon\CarbonInterface;
use DateTime;
use Illuminate\View\ComponentAttributeBag;

class MonthPicker extends Field
{
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.month-picker';

    protected $displayFormat = null;

    protected $extraTriggerAttributes = [];

    protected $firstDayOfWeek = null;

    protected $format = null;

    protected $isWithoutDate = false;

    protected $isWithoutSeconds = false;

    protected $isWithoutTime = false;

    protected $maxDate = null;

    protected $minDate = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (MonthPicker $component, callable $set, $state): void {
            if (! $state instanceof CarbonInterface) {
                return;
            }

            $state = $state->format($component->getFormat());

            $set($component, $state);
        });

        $this->resetFirstDayOfWeek();

        $this->rule('date', $this->hasDate());
    }

    public function displayFormat(string | callable $format): static
    {
        $this->displayFormat = $format;

        return $this;
    }

    public function extraTriggerAttributes(array | callable $attributes): static
    {
        $this->extraTriggerAttributes = $attributes;

        return $this;
    }

    public function firstDayOfWeek(int | callable $day): static
    {
        if ($day < 0 || $day > 7) {
            $day = $this->getDefaultFirstDayOfWeek();
        }

        $this->firstDayOfWeek = $day;

        return $this;
    }

    public function format(string | callable $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function maxDate(DateTime | string | callable $date): static
    {
        $this->maxDate = $date;

        $this->rule(function () use ($date) {
            $date = $this->evaluate($date);

            if ($date instanceof DateTime) {
                $date = $date->format('Y-m-d');
            }

            return "before_or_equal:{$date}";
        }, fn (): bool => (bool) $this->evaluate($date));

        return $this;
    }

    public function minDate(DateTime | string | callable $date): static
    {
        $this->minDate = $date;

        $this->rule(function () use ($date) {
            $date = $this->evaluate($date);

            if ($date instanceof DateTime) {
                $date = $date->format('Y-m-d');
            }

            return "after_or_equal:{$date}";
        }, fn (): bool => (bool) $this->evaluate($date));

        return $this;
    }

    public function resetFirstDayOfWeek(): static
    {
        $this->firstDayOfWeek($this->getDefaultFirstDayOfWeek());

        return $this;
    }

    public function weekStartsOnMonday(): static
    {
        $this->firstDayOfWeek(1);

        return $this;
    }

    public function weekStartsOnSunday(): static
    {
        $this->firstDayOfWeek(7);

        return $this;
    }

    public function withoutDate(bool | callable $condition = true): static
    {
        $this->isWithoutDate = $condition;

        return $this;
    }

    public function withoutSeconds(bool | callable $condition = true): static
    {
        $this->isWithoutSeconds = $condition;

        return $this;
    }

    public function withoutTime(bool | callable $condition = true): static
    {
        $this->isWithoutTime = $condition;

        return $this;
    }

    public function getDisplayFormat(): string
    {
        $format = $this->evaluate($this->displayFormat);

        if ($format) {
            return $format;
        }

        $format = $this->hasDate() ? 'M j, Y' : '';

        if (! $this->hasTime()) {
            return $format;
        }

        $format = $format ? "{$format} H:i" : 'H:i';

        if (! $this->hasSeconds()) {
            return $format;
        }

        return "{$format}:s";
    }

    public function getExtraTriggerAttributes(): array
    {
        return $this->evaluate($this->extraTriggerAttributes);
    }

    public function getExtraTriggerAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraTriggerAttributes());
    }

    public function getFirstDayOfWeek(): int
    {
        return $this->evaluate($this->firstDayOfWeek);
    }

    public function getFormat(): string
    {
        $format = $this->evaluate($this->format);

        if ($format) {
            return $format;
        }

        $format = $this->hasDate() ? 'Y-m-d' : '';

        if (! $this->hasTime()) {
            return $format;
        }

        $format = $format ? "{$format} H:i" : 'H:i';

        if (! $this->hasSeconds()) {
            return $format;
        }

        return "{$format}:s";
    }

    public function getMaxDate(): ?string
    {
        $date = $this->evaluate($this->maxDate);

        if ($date instanceof DateTime) {
            $date = $date->format($this->getFormat());
        }

        return $date;
    }

    public function getMinDate(): ?string
    {
        $date = $this->evaluate($this->minDate);

        if ($date instanceof DateTime) {
            $date = $date->format($this->getFormat());
        }

        return $date;
    }

    public function hasDate(): bool
    {
        return ! $this->isWithoutDate;
    }

    public function hasSeconds(): bool
    {
        return ! $this->isWithoutSeconds;
    }

    public function hasTime(): bool
    {
        return ! $this->isWithoutTime;
    }

    protected function getDefaultFirstDayOfWeek(): int
    {
        return config('forms.components.month_picker.first_day_of_week', 1);
    }
}
