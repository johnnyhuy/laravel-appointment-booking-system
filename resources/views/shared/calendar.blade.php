<table class="table no-margin calendar">
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
                    $cDate = Time::parse($pDate->toDateString())->startOfMonth()->startOfWeek()->addDays($days)->addWeeks($weeks);
                @endphp
                <td class="calendar__day calendar__day--block {{ $cDate->month != $pDate->month ? 'calendar__day--disabled' : null }}">
                    @if ($cDate->month == $pDate->month)
                        <div class="calendar__day-label">{{ $cDate->format('d') }}</div>
                        @if ($type == 'customer')
                            <div class="item">
                                @if ($employeeID)
                                    @if ($items = $employee->availableTimes($cDate->toDateString()))
                                        @foreach ($items as $item)
                                            <section class="item__block">
                                                <div class="item__name">{{ firstChar($employee->firstname, true) }} {{ $employee->lastname }}</div>
                                                <div class="item__time">{{ toTime($item['start_time'], false) }} - {{ toTime($item['end_time'], false) }}</div>
                                            </section>
                                        @endforeach
                                    @endif
                                @else
                                    @foreach (Employee::all() as $employee)
                                        @if ($items = $employee->availableTimes($cDate->toDateString()))
                                            @foreach ($items as $item)
                                                <section class="item__block">
                                                    <div class="item__name">{{ firstChar($employee->firstname, true) }} {{ $employee->lastname }}</div>
                                                    <div class="item__time">{{ toTime($item['start_time'], false) }} - {{ toTime($item['end_time'], false) }}</div>
                                                </section>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        @elseif ($type == 'admin')
                            <div class="item">
                                @foreach ($items as $item)
                                    @if ($item->date == $cDate->toDateString())
                                        <section class="item__block item__block--padding" data-toggle="tooltip" data-placement="top" title="{{ $item->employee->firstname }} {{ $item->employee->lastname }} - {{ $item->employee->title }}">
                                            <a title="Edit this working time" href="/admin/roster/{{ $pDate->format('m-Y') }}/{{ $item->employee->id }}/{{ $item->id }}/edit" class="item__edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                            <div class="item__name">
                                                {{ substr($item->employee->firstname, 0, 1) . '. ' . $item->employee->lastname }}
                                            </div>
                                            <div class="item__time">{{ Time::parse($item->start_time)->format('H:i') . ' - ' . Time::parse($item->end_time)->format('H:i') }}</div>
                                        </section>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endif
                </td>
            @endfor
        </tr>
    @endfor
</table>
