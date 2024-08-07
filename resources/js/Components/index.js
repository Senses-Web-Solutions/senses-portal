// Buttons
import SeButton from './Ui/Buttons/SeButton.vue';
import DeleteButton from './Ui/Buttons/DeleteButton.vue';
import PlusButton from './Ui/Buttons/PlusButton.vue';
import AddButton from './Ui/Buttons/AddButton.vue';
import EditButton from './Ui/Buttons/EditButton.vue';
import PrimaryButton from './Ui/Buttons/PrimaryButton.vue';
import InfoButton from './Ui/Buttons/InfoButton.vue';
import DangerButton from './Ui/Buttons/DangerButton.vue';
import SecondaryButton from './Ui/Buttons/SecondaryButton.vue';

import AdditionalOptionsMenu from './Ui/Menu/AdditionalOptionsMenu.vue';

import BasicFields from './Senses/Common/BasicFields.vue'; // only for testing, can be removed
import DataHydrator from './Senses/Common/DataHydrator.vue';

import NotificationAside from './Senses/Notifications/NotificationAside.vue';
import NotificationCard from './Senses/Notifications/NotificationCard.vue';
import NotificationTable from './Senses/Notifications/NotificationTable.vue';

import Collapse from './Ui/Collapse/Collapse.vue';
import CollapseGroup from './Ui/Collapse/CollapseGroup.vue';
import SeMenu from './Ui/Menu/SeMenu.vue';
import SeTableMenuAside from './Ui/Menu/SeTableMenuAside.vue';
import ReorderableList from './Ui/Lists/ReorderableList.vue';
import EmptyState from './Ui/EmptyState.vue';
import Colour from './Ui/Colour.vue';
import Error from './Ui/Errors/Error.vue';
import Panel from './Ui/Panels/Panel.vue';
import PageHeaderSkeleton from './Ui/Skeletons/PageHeaderSkeleton.vue';
import CardSkeleton from './Ui/Skeletons/CardSkeleton.vue';
import StrongText from './Ui/Text/StrongText.vue';
import BoldText from './Ui/Text/BoldText.vue';
import Text from './Ui/Text/Text.vue';
import HighlightText from './Ui/Text/HighlightText.vue';
import Card from './Ui/Cards/Card.vue';
import SeColour from './Ui/Inputs/SeColour.vue';
import ErrorView from './Ui/Errors/ErrorView.vue';
import AbilitiesReseeder from './Senses/Abilities/Reseed.vue';

import UserImpersonationForm from './Senses/Users/UserImpersonationForm.vue';

import Dashboard from './Senses/Dashboard.vue';

import SeChart from './Senses/UI/SeChart.vue';

// Models
import UserTable from './Senses/Users/UserTable.vue';
import UserForm from './Senses/Users/UserForm.vue';
import StatusTable from './Senses/Statuses/StatusTable.vue';
import StatusForm from './Senses/Statuses/StatusForm.vue';
import TagTable from './Senses/Tags/TagTable.vue';
import TagForm from './Senses/Tags/TagForm.vue';
import TagGroupTable from './Senses/TagGroups/TagGroupTable.vue';
import TagGroupForm from './Senses/TagGroups/TagGroupForm.vue';
import StatusGroupTable from './Senses/StatusGroups/StatusGroupTable.vue';
import StatusGroupForm from './Senses/StatusGroups/StatusGroupForm.vue';
import StatusGroupAdditionalInformation from './Senses/StatusGroups/StatusGroupAdditionalInformation.vue';
import TagGroupAdditionalInformation from './Senses/TagGroups/TagGroupAdditionalInformation.vue';
import AbilityTable from './Senses/Abilities/AbilityTable.vue';
import AbilityGroupTable from './Senses/AbilityGroups/AbilityGroupTable.vue';
import AbilityGroupForm from './Senses/AbilityGroups/AbilityGroupForm.vue';
import AbilityGroupAbilities from './Senses/AbilityGroups/AbilityGroupAbilities.vue';
import CompanyTable from './Senses/Companies/CompanyTable.vue';
import CompanyForm from './Senses/Companies/CompanyForm.vue';
import ServerTable from './Senses/Servers/ServerTable.vue';
import ServerForm from './Senses/Servers/ServerForm.vue';
import ServerDashboard from './Senses/Servers/ServerDashboard.vue';
import ServerSetup from './Senses/Servers/ServerSetup.vue';
import ServerList from './Senses/Servers/ServerList.vue';
import RevenueTable from './Senses/Revenues/RevenueTable.vue';
import RevenueForm from './Senses/Revenues/RevenueForm.vue';
import SubscriptionTable from './Senses/Subscriptions/SubscriptionTable.vue';
import SubscriptionForm from './Senses/Subscriptions/SubscriptionForm.vue';
import CommunicationLogTable from './Senses/CommunicationLogs/CommunicationLogTable.vue';
import CommunicationLogForm from './Senses/CommunicationLogs/CommunicationLogForm.vue';

import ChatInbox from "./Senses/Chats/ChatInbox.vue";
import ChatHistoricalInbox from "./Senses/Chats/ChatHistoricalInbox.vue";
import ChatActions from "./Senses/Chats/ChatActions.vue";
import AllowedChatSites from "./Senses/AllowedChatSites/AllowedChatSites.vue";
import CompanyAllowedChatSites from "./Senses/AllowedChatSites/CompanyAllowedChatSites.vue";
import AllowedChatSiteActions from "./Senses/AllowedChatSites/AllowedChatSiteActions.vue";
import AllowedChatSiteForm from "./Senses/AllowedChatSites/AllowedChatSiteForm.vue";
import AllowedChatSiteView from "./Senses/AllowedChatSites/AllowedChatSiteView.vue";

import ChatInviteForm from "./Senses/Chats/ChatInviteForm.vue";

import GalleryModal from "./Senses/Files/GalleryModal.vue";
import LeaveChatModal from "./Senses/Chats/LeaveChatModal.vue";
import DeleteChatModal from "./Senses/Chats/DeleteChatModal.vue";
import CompleteChatModal from "./Senses/Chats/CompleteChatModal.vue";
import ChatReviewTable from "./Senses/ChatReviews/ChatReviewTable.vue";
import AgentChatReviewTable from "./Senses/ChatReviews/AgentChatReviewTable.vue";
import CompanyChatUserTable from "./Senses/ChatUsers/CompanyChatUserTable.vue";
import AgentChatReviews from "./Senses/ChatReviews/AgentChatReviews.vue";
import ChatUserShow from "./Senses/ChatUsers/ChatUserShow.vue";
import AgentChatsStats from './Senses/Chats/AgentChatsStats.vue';
import ChatUserForm from "./Senses/ChatUsers/ChatUserForm.vue";

// Canned Messages
// import CannedMessageTable from "./Senses/CannedMessages/CannedMessageTable.vue";
import UserCannedMessageTable from "./Senses/CannedMessages/UserCannedMessageTable.vue";
import UserCannedMessageForm from "./Senses/CannedMessages/UserCannedMessageForm.vue";
// ----- GENERATOR A -----

export {
    // Buttons
    SeButton,
    DeleteButton,
    PlusButton,
    AddButton,
    EditButton,
    PrimaryButton,
    InfoButton,
    DangerButton,
    SecondaryButton,

    // Common
    AdditionalOptionsMenu,
    BasicFields,
    DataHydrator,

    // UI
    Collapse,
    CollapseGroup,
    SeMenu,
    SeTableMenuAside,
    ReorderableList,
    EmptyState,
    Colour,
    Error,
    Panel,
    PageHeaderSkeleton,
    CardSkeleton,
    StrongText,
    BoldText,
    Text,
    HighlightText,
    Card,
    SeColour,
    ErrorView,
    AbilitiesReseeder,

    // Notifications
    NotificationAside,
    NotificationCard,
    NotificationTable,
    UserImpersonationForm,
    Dashboard,
    SeChart,

    // Models
    UserTable,
    UserForm,
    StatusTable,
    StatusForm,
    TagTable,
    TagForm,
    TagGroupTable,
    TagGroupForm,
    StatusGroupTable,
    StatusGroupForm,
    StatusGroupAdditionalInformation,
    TagGroupAdditionalInformation,
    AbilityTable,
    AbilityGroupTable,
    AbilityGroupForm,
    AbilityGroupAbilities,
    CompanyTable,
    CompanyForm,
    ServerTable,
    ServerForm,
    ServerDashboard,
    ServerSetup,
    ServerList,
    RevenueTable,
    RevenueForm,
    SubscriptionTable,
    SubscriptionForm,
    CommunicationLogTable,
    CommunicationLogForm,
    ChatInbox,
    ChatActions,
    AllowedChatSites,
    CompanyAllowedChatSites,
    AllowedChatSiteActions,
    AllowedChatSiteForm,
    AllowedChatSiteView,
    ChatInviteForm,
    GalleryModal,
    LeaveChatModal,
    DeleteChatModal,
    CompleteChatModal,
    ChatReviewTable,
    AgentChatReviewTable,
    CompanyChatUserTable,
    AgentChatReviews,
    ChatUserShow,
    ChatHistoricalInbox,
    AgentChatsStats,
    ChatUserForm,

    // Canned Messages
    // CannedMessageTable,
    UserCannedMessageTable,
    UserCannedMessageForm,
    // ----- GENERATOR B -----
};
