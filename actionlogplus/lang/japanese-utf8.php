<?php
/*
 * ActionLogPlus plugin for Nucleus CMS
 * ver. 0.2.3
 * Written By Mocchi, Jan. 23, 2008
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 */

$this->langArray['Adding some actions for action log. The logs are written out and removed when clearing logs or the number of logs over 400, then the action recorded. Adding the function for downloading the logs written out.']='管理操作履歴に保存される管理操作を増やします。履歴を削除する時もしくは履歴が400件に達した時にテキストに書き出し、履歴が消去された後にその履歴を残します。履歴のダウンロード機能も提供します。';

$this->langArray['Record the logs about MEMBER? (changing settings, deleting)']='メンバー関連の履歴（登録・変更・削除）を記録しますか？';
$this->langArray['Record the logs about BLOG? (creating, changing settings, deleting)']='ウェブログ関連の履歴（作成・変更・削除）を記録しますか？';
$this->langArray['Record the logs about TEAM? (changing admin, removing one)']='チーム関連の履歴（チームへのメンバー追加・削除・管理権限の変更）を記録しますか？';
$this->langArray['Record the logs about CATEGORY? (creating, changing settings, deleting)']='カテゴリー関連の履歴（追加・変更・削除）を記録しますか？';
$this->langArray['Record the logs about ITEM? (adding, editing, deleting)']='アイテム関連の履歴（追加・変更・削除・移動）を記録しますか？';
$this->langArray['Record the logs about BAN OF IPs? (adding, removing)']='禁止IP関連の履歴（登録・削除）を記録しますか？';
$this->langArray['Record the logs about PLUGINS? (installing, changing global settings, uninstalling)']='プラグイン関連の履歴（追加・削除）とグローバル・オプションの変更を記録しますか？';
$this->langArray['Record the logs about BACKUP? (creating)']='バックアップ・データの作成を記録しますか？';
$this->langArray['Record the logs about changing GLOBAL SETTINGS?']='グローバル設定の変更を記録しますか';
$this->langArray['Record the logs about MEDIA FILES?']='外部ファイル関連の履歴（ファイルアップロード）を保存しますか？';

$this->langArray['Add new item']='アイテムの追加';
$this->langArray['Edit item']='アイテムの編集';
$this->langArray['Delete item']='アイテムの削除';
$this->langArray['Move an item to other category of weblog']='アイテムをウェブログのカテゴリに移動';
$this->langArray['Add category']='カテゴリーの追加';
$this->langArray['Change settings for category']='カテゴリーの設定変更';
$this->langArray['Delete category']='カテゴリーの削除';
$this->langArray['Move category from/to weblog']='カテゴリーをウェブログからウェブログに移動';
$this->langArray['Add new weblog']='新しいウェブログの追加';
$this->langArray['Change settings for weblog']='ウェブログの設定変更';
$this->langArray['Delete weblog']='ウェブログの削除';
$this->langArray['Install plugin']='プラグインの追加';
$this->langArray['Change plugin options for global settings']='プラグインのグローバル・オプション変更';
$this->langArray['Uninstall plugin']='プラグインの削除';
$this->langArray['Add member']='メンバーの追加';
$this->langArray['Change settings for member']='メンバーの設定変更';
$this->langArray['Delete member']='メンバーの削除';
$this->langArray['Add member to team']='チームにメンバーを追加';
$this->langArray['Change the rights for team administration']='チーム内のメンバーの管理者権限を変更';
$this->langArray['Remove member from team']='チームからメンバーを削除';
$this->langArray['Add ban of IP']='ウェブログに禁止IPを設定';
$this->langArray['Remove ban of IP']='ウェブログの禁止IPを解除';
$this->langArray['Logout']='ログアウト';
$this->langArray['Preserve and delete action logs']='管理操作履歴の保存と消去';
$this->langArray['Create backup data']='ウェブログのバックアップ・データを作成';
$this->langArray['Change global settings']='グローバル設定の変更';
$this->langArray['File Upload']='ファイル・アップロード';
$this->langArray['File Rename']='ファイル名変更';
$this->langArray['File Erase']='ファイル削除';

$this->langArray['Auto-write out for action logs']='管理操作履歴の自動書き出し';

$this->langArray['Directory for Action Logs']='管理操作履歴の保存ファイルの場所';
$this->langArray['Action Log List']='保存されている管理操作履歴のリスト';
$this->langArray['Download']='ダウンロード';
$this->langArray['Log Date']='ログ取得日';
$this->langArray['Log Time']='ログ取得時刻';
$this->langArray['Function']='機能';
?>