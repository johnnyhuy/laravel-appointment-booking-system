<h1>{{ $date->format('F Y') }}</h1>
<div class="table-responsive dash__table-wrapper">
    <table class="table table--no-margin dash__table calender">
        <tr>
            <th class="calender__week">Week</th>
            <th class="calender__day">Monday</th>
            <th class="calender__day">Tuesday</th>
            <th class="calender__day">Wednesday</th>
            <th class="calender__day">Thursday</th>
            <th class="calender__day">Friday</th>
            <th class="calender__day">Saturday</th>
            <th class="calender__day">Sunday</th>
        </tr>
        @for ($weeks = 0; $weeks < 5; $weeks++)
            <tr>
                <td class="calender__week calender__week--label">{{ $weeks + 1 }}</td>
                @for ($days = 0; $days < 7; $days++)
                    @php
                        $cDate = Carbon\Carbon::parse($date->toDateString())->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks);
                    @endphp
                    <td class="calender__day calender__day--block {{ $cDate->month != $date->month ? 'calender__day--disabled' : null }}">
                        @if ($cDate->month == $date->month)
                        <div class="calender__day-label">{{ $cDate->format('d') }}</div>
                        <div class="working-time">
                            @foreach ($roster as $workingTime)
                                @php
                                    $wDate = Carbon\Carbon::parse($workingTime->date);
                                @endphp
                                @if ($workingTime->date == $cDate->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks)->toDateString())
                                    <section class="working-time__block">
                                        <a title="Edit this working time" href="/admin/roster/{{ $workingTime->id }}/edit" class="working-time__edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                        <div class="working-time__name">{{ $workingTime->employee->firstname . ' ' . $workingTime->employee->lastname }}</div>
                                        <div class="working-time__title">{{ $workingTime->employee->title }}</div>
                                        <div class="working-time__time">{{ Carbon\Carbon::parse($workingTime->start_time)->format('h:i A') . ' - ' . Carbon\Carbon::parse($workingTime->end_time)->format('h:i A') }}</div>
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
