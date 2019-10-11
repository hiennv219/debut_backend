<?php

namespace App;

class Consts{
  const CACHE_LIVE_TIME_DEFAULT = 60;
  const QUEUE_SMS = 'sms';
  const ACTIVE = 1;
  const INACTIVE = 0;

  const CONNECTION_SOCKET = 'sync';
  const QUEUE_SOCKET = 'socket';

  const TYPE_SOCIAL = 0;
  const TYPE_PRIVATE = 1;

  const STATUS_PENDING = 'pending';
  const STATUS_APPROVED = 'approved';
  const STATUS_REJECTED = 'rejected';
}
