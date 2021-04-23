<?php

declare(strict_types = 1);

namespace Drupal\Tests\helfi_tpr\Kernel;

/**
 * Tests TPR Unit migration.
 *
 * @group helfi_tpr
 */
class UnitMigrationTest extends MigrationTestBase {

  /**
   * Tests unit migration.
   *
   * @dataProvider unitMigrationData
   */
  public function testUnitMigration(string $langcode, array $expected) : void {
    $entities = $this->createUnitMigration();
    $this->assertCount(1, $entities);

    foreach ($entities as $entity) {
      /** @var \Drupal\helfi_tpr\Entity\Unit $translation */
      $translation = $entity->getTranslation($langcode);

      $this->assertEquals($expected['id'], $translation->id());
      $this->assertEquals($expected['name'], $translation->label());
      $this->assertEquals($expected['latitude'], $translation->get('latitude')->value);
      $this->assertEquals($expected['longitude'], $translation->get('longitude')->value);
      $this->assertEquals($expected['street_address'], $translation->get('address')->address_line1);
      $this->assertEquals($expected['address_zip'], $translation->get('address')->postal_code);
      $this->assertEquals($expected['address_city'], $translation->get('address')->locality);
      $this->assertEquals('FI', $translation->get('address')->country_code);
      $this->assertEquals($expected['phone'], $translation->get('phone')->value);
      $this->assertEquals($expected['call_charge_info'], $translation->get('call_charge_info')->value);
      $this->assertEquals($expected['www'], $translation->get('www')->uri);
      $this->assertEquals('1', $translation->get('uid')->target_id);
    }
  }

  /**
   * The data provider for unit migration.
   *
   * @return array
   *   The data.
   */
  public function unitMigrationData() : array {
    return [
      [
        'en',
        [
          'id' => 1,
          'name' => 'Name fi 1',
          'latitude' => '60.19',
          'longitude' => '24.76',
          'street_address' => 'Address fi 1',
          'address_zip' => '02180',
          'address_city' => 'Espoo en 1',
          'phone' => '+3581234',
          'call_charge_info' => 'pvm en 1',
          'www' => 'https://localhost/fi/1',
        ],
      ],
      [
        'fi',
        [
          'id' => 1,
          'name' => 'Name fi 1',
          'latitude' => '60.19',
          'longitude' => '24.76',
          'street_address' => 'Address fi 1',
          'address_zip' => '02180',
          'address_city' => 'Espoo fi 1',
          'phone' => '+3581234',
          'call_charge_info' => 'pvm fi 1',
          'www' => 'https://localhost/fi/1',
        ],
      ],
      [
        'sv',
        [
          'id' => 1,
          'name' => 'Name sv 1',
          'latitude' => '60.19',
          'longitude' => '24.76',
          'street_address' => 'Address sv 1',
          'address_zip' => '02180',
          'address_city' => 'Espoo sv 1',
          'phone' => '+3581234',
          'call_charge_info' => 'pvm sv 1',
          'www' => 'https://localhost/sv/1',
        ],
      ],
    ];
  }

}