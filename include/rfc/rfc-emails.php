<?php

/*
   +----------------------------------------------------------------------+
   | PEAR Web site version 1.0                                            |
   +----------------------------------------------------------------------+
   | Copyright (c) 2001-2003 The PHP Group                                |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.02 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available at through the world-wide-web at                           |
   | http://www.php.net/license/2_02.txt.                                 |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors:       Vincent Gevers <vincent@php.net>                      |
   |                Tobias Schlitt <toby@php.net>                         |
   +----------------------------------------------------------------------+
   $Id$
*/

$proposalEmailTexts = array(

    // $proposal->sendActionEmail("proposal_delete", $_COOKIE["PEAR_USER"], $proposal);
    'proposal_delete' => array(
        'to'        => array (
            'admin'   => '{email_pear_dev}, {email_pear_group}, {owner_name} {owner_email}',
            'user'    => '{email_pear_dev}, {owner_name} {owner_email}'
        ),
        'subject'   => '{pkg_category}::{pkg_name} deleted',
        'text'      => "{actor_name} ({actor_link}) has deleted the proposal for {pkg_category}::{pkg_name}."
    ),

    // $proposal->sendActionEmail("change_status_proposal", $_COOKIE["PEAR_USER"], $proposal);
    'change_status_proposal' => array(
        'to'        => array (
            'admin'   => '{email_pear_dev}, {email_pear_group}, {owner_name} {owner_email}',
            'user'    => '{email_pear_dev}, {owner_name} {owner_email}'
        ),
        'subject'   => 'Proposal for {pkg_category}::{pkg_name}',
        'text'      => "{actor_name} ({actor_link}) proposes {pkg_category}::{pkg_name}.\n\nYou can find more detailed information here:\n {proposal_url}"
    ),

    // $proposal->sendActionEmail("change_status_vote", $_COOKIE["PEAR_USER"], $proposal);
    'change_status_vote' => array(
        'to'        => array (
            'admin'   => '{email_pear_dev}, {email_pear_group}, {owner_name} {owner_email}',
            'user'    => '{email_pear_dev}, {owner_name} {owner_email}'
        ),
        'subject'   => 'Call for votes on {pkg_category}::{pkg_name}',
        'text'      => "{actor_name} ({actor_link}) has initiated the call for votes on {pkg_category}::{pkg_name}.\n\nPlease review the proposal and give your vote here:\n{proposal_url}"
    ),

    // $proposal->sendActionEmail("longened_timeline_sys", $_COOKIE["PEAR_USER"], $proposal);
    'longened_timeline_sys' => array(
        'to'        => array (
            'pearweb'   => '{email_pear_dev}, {email_pear_group}, {owner_name} {owner_email}'
        ),
        'subject'   => 'Extended call for votes on {pkg_category}::{pkg_name}',
        'text'      => "RFC has automatically extended the voting time on {pkg_category}::{pkg_name} until {end_voting_time} because there were not enough votes, yet.\n\nPlease review the proposal and give your vote here:\n{proposal_url}\n\nVoting time is extended only once per proposal."
    ),

    // $proposal->sendActionEmail("longened_timeline_admin", $_COOKIE["PEAR_USER"], $proposal);
    'longened_timeline_admin' => array(
        'to'        => array (
            'admin'   => '{email_pear_dev}, {email_pear_group}, {owner_name} {owner_email}'
        ),
        'subject'   => 'Extended call for votes on {pkg_category}::{pkg_name}',
        'text'      => "{actor_name} ({actor_link}) has extended the voting time on {pkg_category}::{pkg_name} until {end_voting_time}.\n\nPlease review the proposal and give your vote here:\n{proposal_url}"
    ),

    // $proposal->sendActionEmail("change_status_finished", $_COOKIE["PEAR_USER"], $proposal);
    'change_status_finished' => array(
        'to'        => array (
            'pearweb' => '{email_pear_dev}, {email_pear_group}, {owner_name} {owner_email}',
            'user'    => '{email_pear_dev}, {owner_name} {owner_email}'
        ),
        'subject'   => 'Proposal finished {pkg_category}::{pkg_name}',
        'text'      => "RFC has automatically finished the proposal on {pkg_category}::{pkg_name}.\n\n{vote_result}\n\nFurther details on the status of the proposal and the votes can be found here:\n{proposal_url}"
    ),

    // $proposal->sendActionEmail("edit_proposal", $_COOKIE["PEAR_USER"], $proposal);
    'edit_proposal' => array(
        'to'        => array (
            'admin'   => '{email_pear_dev}, {email_pear_group}, {owner_name} {owner_email}',
            'user'    => '{email_pear_dev}, {owner_name} {owner_email}'
        ),
        'subject'   => 'Changes in proposal for {pkg_category}::{pkg_name}',
        'text'      => "{actor_name} ({actor_link}) has edited the proposal for {pkg_category}::{pkg_name}.\n\nChange comment:\n\n{comment}\n\nPlease review the proposal:\n{proposal_url}"
    ),

    // $proposal->sendActionEmail("proposal_vote", $_COOKIE["PEAR_USER"], $proposal);
    'proposal_vote' => array(
        'to'        => array (
            'user'    => '{email_pear_dev}, {actor_name} <{actor_email}>, {owner_name} {owner_email}',
            'admin'   => '{email_pear_dev}, {actor_name} <{actor_email}>, {owner_name} {owner_email}'
        ),
        'subject'   => '{vote_value} for {pkg_category}::{pkg_name}',
        'text'      => "{actor_name} ({actor_link}) has voted {vote_value} on the proposal for {pkg_category}::{pkg_name}.\n\nProposal information:\n{proposal_url}\nVote information:\n{vote_url}{vote_conditional}{comment}"
    ),

    // $proposal->sendActionEmail("proposal_vote", $_COOKIE["PEAR_USER"], $proposal);
    'proposal_comment' => array(
        'to'        => array (
            'user'    => '{email_pear_dev}, {actor_name} <{actor_email}>, {owner_name} {owner_email}',
            'admin'   => '{email_pear_dev}, {actor_name} <{actor_email}>, {owner_name} {owner_email}'
        ),
        'subject'   => 'Comment on {pkg_category}::{pkg_name}',
        'text'      => "{actor_name} ({actor_link}) has commented on the proposal for {pkg_category}::{pkg_name}.\n\nComment:\n\n{comment}\n\nProposal information:\n{proposal_url}"
    ),
);

?>
