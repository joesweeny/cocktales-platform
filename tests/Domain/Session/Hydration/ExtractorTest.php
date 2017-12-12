<?php

namespace Cocktales\Domain\Session\Hydration;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_to_raw_data_converts_a_session_token_into_a_std_class_object()
    {
        $data = Extractor::toRawData(
            new SessionToken(
                new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                new \DateTimeImmutable('2017-12-11 20:20:02'),
                new \DateTimeImmutable('2017-12-11 20:20:02')
            )
        );

        $this->assertEquals('a4a93668-6e61-4a81-93b4-b2404dbe9788', Uuid::createFromBinary($data->token));
        $this->assertEquals('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed', Uuid::createFromBinary($data->user_id));
        $this->assertEquals(1513023602, $data->created_at);
        $this->assertEquals(1513023602, $data->expiry);
    }
}
