<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('availabilities.{availability}.main', function ($availability, $id) {
    return true;
});

Broadcast::channel('categories.{category}.main', function ($category, $id) {
    return true;
});

Broadcast::channel('cost-groups.{cost_group}.main', function ($costGroup, $id) {
    return true;
});

Broadcast::channel('coverages.{coverage}.main', function ($coverage, $id) {
    return true;
});

Broadcast::channel('event-types.{event_type}.main', function ($eventType, $id) {
    return true;
});

Broadcast::channel('facilities.{facility}.main', function ($facility, $id) {
    return true;
});

Broadcast::channel('favourites.{favourite}.main', function ($favourite, $id) {
    return true;
});

Broadcast::channel('files.{file}.main', function ($file, $id) {
    return true;
});

Broadcast::channel('glossaries.{glossary}.main', function ($glossary, $id) {
    return true;
});

Broadcast::channel('keywords.{keyword}.main', function ($keyword, $id) {
    return true;
});

Broadcast::channel('links.{link}.main', function ($link, $id) {
    return true;
});

Broadcast::channel('organisations.{organisation}.main', function ($organisation, $id) {
    return true;
});

Broadcast::channel('organisation-types.{organisation_type}.main', function ($organisationType, $id) {
    return true;
});

Broadcast::channel('pages.{page}.main', function ($page, $id) {
    return true;
});

Broadcast::channel('postals.{postal}.main', function ($postal, $id) {
    return true;
});

Broadcast::channel('reviews.{review}.main', function ($review, $id) {
    return true;
});

Broadcast::channel('services.{service}.main', function ($service, $id) {
    return true;
});

Broadcast::channel('service-types.{service_type}.main', function ($serviceType, $id) {
    return true;
});

Broadcast::channel('statuses.{status}.main', function ($status, $id) {
    return true;
});

Broadcast::channel('status-groups.{status_group}.main', function ($statusGroup, $id) {
    return true;
});

Broadcast::channel('support-groups.{status_group}.main', function ($supportGroup, $id) {
    return true;
});

Broadcast::channel('tags.{tag}.main', function ($tag, $id) {
    return true;
});

Broadcast::channel('tag-groups.{tag_group}.main', function ($tagGroup, $id) {
    return true;
});

Broadcast::channel('target-audiences.{target_audience}.main', function ($targetAudience, $id) {
    return true;
});

Broadcast::channel('users.{user}.main', function ($user, $id) {
    return true;
});

Broadcast::channel('user-settings.{user_setting}.main', function ($userSetting, $id) {
    return true;
});

Broadcast::channel('servers.{server}.main', function ($server, $id) {
    return true;
});

Broadcast::channel('servers.{server}.server-metrics', function ($server, $id) {
    return true;
});

Broadcast::channel('servers.{server}.deploy', function ($server, $id) {
    return true;
});

Broadcast::channel('venues.{venue}.main', function ($venue, $id) {
    return true;
});

// Chats
// Portal Side
Broadcast::channel('companies.{company}.chat', function ($company, $id) {
    return true;
});

Broadcast::channel('companies.{company}.message', function ($company, $id) {
    return true;
});

// Package Side
Broadcast::channel('chats.{chat}.message', function ($chat, $id) {
    return true;
});

Broadcast::channel('users.{user}.notifications', function ($user) {
    return getCurrentUser()->id === $user->id;
});
