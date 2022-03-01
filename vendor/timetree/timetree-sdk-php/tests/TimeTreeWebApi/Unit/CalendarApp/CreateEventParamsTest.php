<?php

namespace Tests\TimeTreeWebApi\Unit\CalendarApp;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use TimeTreeWebApi\CalendarApp\Parameter\AttendeesParams;
use TimeTreeWebApi\CalendarApp\Parameter\CreateEventParams;

class CreateEventParamsTest extends TestCase
{
  public function testGetParamsWhenNotAllday(): void
  {
    $instance = new CreateEventParams(
      "test title",
      "schedule",
      false,
      new DateTime("2020-10-10 09:00:00"),
      new DateTimeZone("Asia/Tokyo"),
      new DateTime("2020-10-10 11:00:00"),
      new DateTimeZone("Asia/Tokyo"),
      "test description",
      "sagrada familia",
      "https://developers.timetreeapp.com/",
      new AttendeesParams([1, 2, 3])
    );
    $params = $instance->getParams();

    $this->assertEquals($params, [
      "data" => [
        "attributes" => [
          "title" => "test title",
          "category" => "schedule",
          "all_day" => false,
          "start_at" => "2020-10-10T09:00:00+0000",
          "start_timezone" => "Asia/Tokyo",
          "end_at" => "2020-10-10T11:00:00+0000",
          "end_timezone" => "Asia/Tokyo",
          "description" => "test description",
          "location" => "sagrada familia",
          "url" => "https://developers.timetreeapp.com/",
        ],
        "relationships" => [
          "attendees" => [
            "data" => [
              ["id" => 1, "type" => "user"],
              ["id" => 2, "type" => "user"],
              ["id" => 3, "type" => "user"],
            ]
          ]
        ]
      ]
    ]);
  }

  public function testGetParamsWhenAllday(): void
  {
    $instance = new CreateEventParams(
      "test title",
      "schedule",
      true,
      new DateTime("2020-10-10"),
      null,
      new DateTime("2020-10-10"),
      null,
      "test description",
    );
    $params = $instance->getParams();

    $this->assertEquals($params, [
      "data" => [
        "attributes" => [
          "title" => "test title",
          "category" => "schedule",
          "all_day" => true,
          "start_at" => "2020-10-10T00:00:00+0000",
          "end_at" => "2020-10-10T00:00:00+0000",
          "description" => "test description",
        ]
      ]
    ]);
  }

  public function testGetParamsWhenCategoryIsKeep(): void
  {
    $instance = new CreateEventParams(
      "test title",
      "keep",
      true,
      null,
      null,
      null,
      null,
      "test description",
    );
    $params = $instance->getParams();

    $this->assertEquals($params, [
      "data" => [
        "attributes" => [
          "title" => "test title",
          "category" => "keep",
          "all_day" => true,
          "description" => "test description",
        ]
      ]
    ]);
  }
}
