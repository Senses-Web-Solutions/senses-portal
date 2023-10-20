import { forIn } from 'lodash-es';
import axios from 'axios';
import useEcho from '../useEcho';
import Maintainer from './Maintainer';

const echo = useEcho();

export default class extends Maintainer {
    constructor(data) {
        super('task', data);
        this.data = data;
        echo.private(`tasks.${this.data.id}.main`).listen('Tasks\\TaskUpdated', this.taskUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.comment-stats`).listen('Comments\\TaskCommentStatsUpdated', this.commentStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.stock-product-assignment-stats`).listen('StockProductAssignments\\TaskStockProductAssignmentStatsUpdated', this.stockProductAssignmentStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.equipment-usage-stats`).listen('EquipmentUsages\\TaskEquipmentUsageStatsUpdated', this.equipmentUsageStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.to-do-stats`).listen('ToDos\\TaskToDoStatsUpdated', this.toDoStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.quote-stats`).listen('Quotes\\TaskQuoteStatsUpdated', this.quoteStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.revenue-stats`).listen('Revenues\\TaskRevenueStatsUpdated', this.revenueStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.task-instruction-stats`).listen('TaskInstructions\\TaskTaskInstructionStatsUpdated', this.taskTaskInstructionStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.requirement-stats`).listen('Requirements\\TaskRequirementStatsUpdated', this.requirementStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.file-stats`).listen('Files\\TaskFileStatsUpdated', this.fileStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.form-stats`).listen('Forms\\TaskFormStatsUpdated', this.formStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.product-request-stats`).listen('ProductRequests\\TaskProductRequestStatsUpdated', this.productRequestStatsUpdate.bind(this));
        echo.private(`tasks.${this.data.id}.assignment-group-stats`).listen('AssignmentGroups\\TaskAssignmentGroupStatsUpdated', this.assignmentGroupStatsUpdate.bind(this));
    }

    commentStatsUpdate(payload) {
        this.data.comments_count = payload.commentsCount;
    }

    stockProductAssignmentStatsUpdate(payload) {
        this.data.stock_product_assignments_count = payload.stockProductAssignmentsCount;
    }

    equipmentUsageStatsUpdate(payload) {
        this.data.equipment_usages_count = payload.equipmentUsagesCount;
    }

    productRequestStatsUpdate(payload) {
        this.data.pending_product_requests_count = payload.pendingProductRequestsCount;
        this.data.ordered_product_requests_count = payload.orderedProductRequestsCount;
        this.data.complete_product_requests_count = payload.completeProductRequestsCount;
        this.data.unfulfilled_product_requests_count = payload.unfulfilledProductRequestsCount;
    }

    taskUpdate(payload) {
        //this event was passing too much, so lamely just go get more ourselves outside of pusher.
        axios.get('/api/v2/tasks/' + payload.task.id).then((response) => {
            forIn(response.data, (value, key) => {
                if (typeof value === 'object' && !Array.isArray(value) && value !== null) {
                    forIn(value, (subValue, subKey) => {
                        if ((key, Object.keys(this.data).includes(key) && key, Object.keys(this.data[key]).includes(subKey))) {
                            this.data[key][subKey] = subValue;
                        }
                    });
                } else {
                    if (Array.isArray(value)) {
                        this.data[key].splice(0);
                        value.forEach((v) => {
                            this.data[key].push(v);
                        });
                    } else {
                        this.data[key] = value;
                    }
                }
            });
        });

        // forIn(payload.task, (value, key) => {
        //     this.data[key] = value;
        // });
    }

    toDoStatsUpdate(payload) {
        this.data.important_to_dos_count = payload.importantToDosCount;
        this.data.to_dos_count = payload.toDosCount;
        this.data.overdue_to_dos_count = payload.overdueToDosCount;
    }

    quoteStatsUpdate(payload) {
        this.data.quotes_count = payload.quotesCount;
        this.data.quotes_approved_count = payload.quotesApprovedCount;
        this.data.quotes_declined_count = payload.quotesDeclinedCount;
        this.data.quotes_pending_count = payload.quotesPendingCount;
        this.data.quotes_total = payload.quotesTotal;
        this.data.quotes_pending_total = payload.quotesPendingTotal;
        this.data.quotes_approved_total = payload.quotesApprovedTotal;
        this.data.quotes_declined_total = payload.quotesDeclinedTotal;
    }

    revenueStatsUpdate(payload) {
        this.data.earnings_total = payload.earningsTotal;
        this.data.earnings_count = payload.earningsCount;

        this.data.individual_earnings_total = payload.individualEarningsTotal;
        this.data.individual_earnings_count = payload.individualEarningsCount;

        this.data.combined_earnings_total = payload.combinedEarningsTotal;
        this.data.combined_earnings_count = payload.combinedEarningsCount;

        this.data.invoiced_total = payload.invoicedTotal;
        this.data.invoices_count = payload.invoicesCount;

        this.data.uninvoiced_total = payload.uninvoicedTotal;

        this.data.costs_total = payload.costsTotal;
        this.data.costs_count = payload.costsCount;

        this.data.individual_costs_total = payload.individualCostsTotal;
        this.data.individual_costs_count = payload.individualCostsCount;

        this.data.combined_costs_total = payload.combinedCostsTotal;
        this.data.combined_costs_count = payload.combinedCostsCount;

        this.data.profit_total = payload.profitTotal;


        this.data.intracompany_to_total = payload.intracompanyToTotal;
        this.data.intracompany_to_count = payload.intracompanyToCount;

        this.data.intracompany_from_total = payload.intracompanyFromTotal;
        this.data.intracompany_from_count = payload.intracompanyFromCount;


        this.data.payment_applications_total = payload.paymentApplicationsTotal;
        this.data.payment_applications_count = payload.paymentApplicationsCount;
    }

    requirementStatsUpdate(payload) {
        this.data.complete_requirements_count = payload.completeRequirementsCount;
        this.data.due_requirements_count = payload.dueRequirementsCount;
        this.data.complete_customer_requirements_count = payload.completeRequirementsCount;
        this.data.due_customer_requirements_count = payload.dueCustomerRequirementsCount;
    }

    taskTaskInstructionStatsUpdate(payload) {
        this.data.task_instructions_count = payload.taskInstructionsCount;
    }

    fileStatsUpdate(payload) {
        this.data.files_count = payload.filesCount;
    }

    formStatsUpdate(payload) {
        this.data.forms_count = payload.formsCount;
    }

    assignmentGroupStatsUpdate(payload) {
        this.data.assignment_groups_count = payload.assignmentGroupsCount;
        this.data.assignment_groups_completed_count = payload.assignmentGroupsCompletedCount;
        this.data.assignment_groups_abandoned_count = payload.assignmentGroupsAbandonedCount;
        this.data.assignment_groups_suspended_count = payload.assignmentGroupsSuspendedCount;

        this.data.first_shift_start_date = payload.firstShiftStartDate;
        this.data.first_shift_end_date = payload.firstShiftEndDate;
        this.data.last_shift_start_date = payload.lastShiftStartDate;
        this.data.last_shift_end_date = payload.lastShiftEndDate;

        this.data.site_first_attended_date = payload.siteFirstAttendedDate;
        this.data.site_last_attended_date = payload.siteLastAttendedDate;

        this.data.shift_duration_average = payload.shiftDurationAverage;
        this.data.shift_travel_duration_average = payload.shiftTravelDurationAverage;
        this.data.shift_duration_average = payload.shiftDurationAverage;
        this.data.shift_site_duration_average = payload.shiftSiteDurationAverage;

        this.data.shift_duration_total = payload.shiftDurationTotal;
        this.data.shift_travel_duration_total = payload.shiftTravelDurationTotal;
        this.data.shift_distance_total = payload.shiftDistanceTotal;
        this.data.shift_site_duration_total = payload.shiftSiteDurationTotal;

        this.data.site_visit_count = payload.siteVisitCount;
    }

    destroy() {
        echo.leave(`tasks.${this.data.id}.main`);
        echo.leave(`tasks.${this.data.id}.file-stats`);
        echo.leave(`tasks.${this.data.id}.to-do-stats`);
        echo.leave(`tasks.${this.data.id}.quote-stats`);
        echo.leave(`tasks.${this.data.id}.revenue-stats`);
        echo.leave(`tasks.${this.data.id}.comment-stats`);
        echo.leave(`tasks.${this.data.id}.requirement-stats`);
        echo.leave(`tasks.${this.data.id}.product-request-stats`);
        echo.leave(`tasks.${this.data.id}.task-instruction-stats`);
        echo.leave(`tasks.${this.data.id}.assignment-group-stats`);
    }
}
