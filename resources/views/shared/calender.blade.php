<h1>{{ $date->format('F Y') }}</h1>
<div class="table-responsive dash__table-wrapper calendar">
    <table class="table no-margin dash__table">
        <tr>
            <th class="calendar__day">Monday</th>
            <th class="calendar__day">Tuesday</th>
            <th class="calendar__day">Wednesday</th>
            <th class="calendar__day">Thursday</th>
            <th class="calendar__day">Friday</th>
            <th class="calendar__day">Saturday</th>
            <th class="calendar__day">Sunday</th>
        </tr>
        @for ($weeks = 0; $weeks < 5; $weeks++)
            <tr>
                @for ($days = 0; $days < 7; $days++)
                    @php
                        $cDate = Carbon\Carbon::parse($date->toDateString())->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks);
                    @endphp
                    <td class="calendar__day calendar__day--block {{ $cDate->month != $date->month ? 'calendar__day--disabled' : null }}">
                        @if ($cDate->month == $date->month)
                        <div class="calendar__day-label">{{ $cDate->format('d') }}</div>
                        <div class="working-time">
                            @foreach ($roster as $workingTime)
                                @php
                                    $wDate = Carbon\Carbon::parse($workingTime->date);
                                @endphp
                                @if ($workingTime->date == $cDate->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks)->toDateString())
                                    <section class="working-time__block" data-toggle="tooltip" data-placement="top" title="{{ $workingTime->employee->firstname }} {{ $workingTime->employee->lastname }} - {{ $workingTime->employee->title }}">
                                        <a title="Edit this working time" href="/admin/roster/{{ $wDate->format('m-Y') . '/' . $workingTime->employee->id . '/' . $workingTime->id }}/edit" class="working-time__edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                        <div class="working-time__name">
                                            {{ substr($workingTime->employee->firstname, 0, 1) . '. ' . $workingTime->employee->lastname }}
                                        </div>
                                        <div class="working-time__time">{{ Carbon\Carbon::parse($workingTime->start_time)->format('H:i') . ' - ' . Carbon\Carbon::parse($workingTime->end_time)->format('H:i') }}</div>
                                    </section>
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </table>
</div>
