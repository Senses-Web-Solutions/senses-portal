<template>
    <div class="lg:flex lg:flex-col">
        <!-- <h2 class="text-2xl font-medium leading-6 text-zinc-900" data-component="SectionTitle">Profile Planner</h2> -->
        <header class="relative z-20 flex items-center justify-between border-b border-zinc-200 pb-2 lg:flex-none">
            <h3 class="month-year-header text-lg font-semibold text-zinc-900"></h3>
            <div class="flex items-center">
                <div class="flex items-center rounded-md shadow-sm md:items-stretch">
                    <button type="button" @click="previousMonth"
                        class="border-r-1 flex items-center justify-center rounded-l-md border border-zinc-300 bg-white py-2 pl-3 pr-4 text-zinc-400 hover:text-zinc-500 focus:relative md:w-9 md:px-2 md:hover:bg-zinc-50">
                        <span class="sr-only">Previous month</span>
                        <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                    </button>

                    <button type="button" @click="nextMonth"
                        class="flex items-center justify-center rounded-r-md border border-l-0 border-zinc-300 bg-white py-2 pl-4 pr-3 text-zinc-400 hover:text-zinc-500 focus:relative md:w-9 md:px-2 md:hover:bg-zinc-50">
                        <span class="sr-only">Next month</span>
                        <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </header>
        <div class="shadow ring-1 ring-black ring-opacity-5 lg:flex lg:flex-auto lg:flex-col">
            <div class="grid grid-cols-7 gap-px border-b border-zinc-300 bg-zinc-200 text-center text-xs font-semibold leading-6 text-zinc-700 lg:flex-none">
                <div class="bg-white py-2">
                    M<span class="sr-only sm:not-sr-only">on</span>
                </div>
                <div class="bg-white py-2">
                    T<span class="sr-only sm:not-sr-only">ue</span>
                </div>
                <div class="bg-white py-2">
                    W<span class="sr-only sm:not-sr-only">ed</span>
                </div>
                <div class="bg-white py-2">
                    T<span class="sr-only sm:not-sr-only">hu</span>
                </div>
                <div class="bg-white py-2">
                    F<span class="sr-only sm:not-sr-only">ri</span>
                </div>
                <div class="bg-white py-2">
                    S<span class="sr-only sm:not-sr-only">at</span>
                </div>
                <div class="bg-white py-2">
                    S<span class="sr-only sm:not-sr-only">un</span>
                </div>
            </div>
            <div class="flex bg-zinc-200 text-xs leading-6 text-zinc-700 lg:flex-auto" style="min-height: 484px">
                <IndeterminateLoadingBarVue v-if="state.is(PageState.LOADING)" class="w-full" />

                <div v-else class="lg:max-grid-rows-6 hidden w-full lg:grid lg:grid-cols-7 lg:gap-px">
                    <div v-for="day in days" :key="day.date" :class="[
                        day.isCurrentMonth
                            ? 'bg-white'
                            : 'bg-zinc-100 text-zinc-500',
                        'relative py-2 px-3',
                    ]" style="min-height: 96px">
                        <time :datetime="day.date" :class="
                            day.isToday
                                ? 'flex h-6 w-6 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white'
                                : undefined
                        ">
                            {{ day.date.split('-').pop().replace(/^0/, '') }}
                        </time>
                        <span v-if="day.eventCount < 4">
                            <ol v-if="day.events !== null" class="mt-2">
                                <li v-for="event in day.events" :key="event.id">
                                    <Tooltip>
                                        <div class="group flex" @click="eventClicked(event)">
                                            <p class="leading-4 flex-auto cursor-pointer truncate font-medium text-zinc-900 group-hover:text-indigo-600">
                                                {{ (Array.isArray(event.lines[0]) && event.lines[0][0]) ? event.lines[0][0] : event.lines[0] }}
                                            </p>
                                            <time v-if="event.type == 'assignment-group'" class="leading-4 ml-3 hidden flex-none text-zinc-500 group-hover:text-indigo-600 xl:block">
                                                {{
                                                    new Date(
                                                        event.start_date
                                                    ).toLocaleTimeString([], {
                                                        hour: '2-digit',
                                                        minute: '2-digit',
                                                    })
                                                }}
                                            </time>
                                        </div>

                                        <template #content>
                                            <p v-if="event.type == 'assignment-group'" class="flex-auto cursor-pointer truncate font-medium mb-0">
                                                {{ event.lines[0].join(', ') }}
                                            </p>
                                            <div v-else>{{ event.lines[0] }}</div>
                                        </template>
                                    </Tooltip>
                                </li>
                            </ol>
                        </span>
                        <span v-else>
                            <div @click="multiEventClicked(day.events)" class="mt-2 flex cursor-pointer text-zinc-900 hover:text-indigo-600">
                                {{ day.eventCount }} events
                            </div>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/solid';
import axios from 'axios';
import { endOfMonth, startOfMonth, startOfWeek, endOfWeek, format } from 'date-fns';
import PageState from '../../../States/PageState';
import IndeterminateLoadingBarVue from '../../Ui/IndeterminateLoadingBar.vue';
import Tooltip from '../../Ui/Tooltip.vue';

import eventHub from '../../../Support/EventHub';

export default {
    data() {
        return {
            date: new Date(),
            startDate: null,
            endDate: null,
            startDateFriendly: null,
            endDateFriendly: null,
            days: null,
            calLoading: false,
            startOfMonth: null,
            endOfMonth: null,
            startOfWeek: null,
            endOfWeek: null,
            state: new PageState(),
            PageState,
            months: [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
            ],
        };
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
    },
    components: {
        IndeterminateLoadingBarVue,
        ChevronRightIcon,
        Tooltip,
        ChevronLeftIcon,
    },
    methods: {
        format,
        eventClicked(event) {
            if (event.type == 'holiday') {
                this.$asides.push('HolidayView', { id: event.id });
            } else if (event.type === 'assignment-group') {
                this.$asides.push('AssignmentGroupView', { id: event.id });
            } else if (event.type === 'pending-holiday') {
                this.$asides.push('HolidayView', { id: event.id });
            } else if (event.type === 'absence') {
                this.$asides.push('AbsenceView', { id: event.id });
            } else if (event.type === 'unavailability') {
                this.$asides.push('UnavailabilityView', { id: event.id });
            } else if (['qualification', 'qualification-expiry', 'qualification-renewal', 'qualification-reminder'].includes(event.type)) {
                this.$asides.push('UserQualificationView', { id: event.id });
            } else if (event.type === 'toil') {
                this.$asides.push('ToilView', { id: event.id });
            } else if (event.type === 'pending-toil') {
                this.$asides.push('ToilView', { id: event.id });
            }
        },
        multiEventClicked(events) {

        },
        renderCalendar() {
            this.state.set(PageState.LOADING);
            axios
                .get(
                    '/api/v2/calendar/users/' +
                    this.data.id +
                    '/' +
                    this.startDateFriendly +
                    '/' +
                    this.endDateFriendly
                )
                .then((response) => {
                    this.days = response.data;
                    this.state.set(PageState.IDLE);
                });
        },

        generateStartDates() {
            this.startOfMonth = startOfMonth(this.date);
            this.endOfMonth = endOfMonth(this.date);

            this.startDate = startOfWeek(this.startOfMonth, {
                weekStartsOn: 1,
            });
            this.endDate = endOfWeek(this.endOfMonth, { weekStartsOn: 1 });

            this.startDateFriendly =
                this.startDate.getFullYear() +
                '-' +
                (this.startDate.getMonth() + 1) +
                '-' +
                this.startDate.getDate();
            this.endDateFriendly =
                this.endDate.getFullYear() +
                '-' +
                (this.endDate.getMonth() + 1) +
                '-' +
                this.endDate.getDate();
        },

        previousMonth() {
            this.date.setMonth(this.date.getMonth() - 1);
            this.generateStartDates();
            this.renderCalendar();
            this.setMonthString();
        },

        nextMonth() {
            this.date.setMonth(this.date.getMonth() + 1);
            this.generateStartDates();
            this.renderCalendar();
            this.setMonthString();
        },

        setMonthString() {
            document.querySelector('.month-year-header').innerHTML =
                this.months[this.date.getMonth()] +
                ' ' +
                this.date.getFullYear();
        },
    },
    mounted() {
        this.generateStartDates();
        this.renderCalendar();
        this.setMonthString();

        eventHub.on('*', (type) => {
            this.renderCalendar();
        });
    },
};
</script>
