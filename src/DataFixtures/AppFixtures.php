<?php

namespace App\DataFixtures;

use App\Entity\Region;
use App\Entity\Room;
use App\Entity\Owner;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    // définit un nom de référence pour une instance de Region
    public const IDF_REGION_REFERENCE = 'idf-region';

    public function load(ObjectManager $manager)
    {
    
    $owner = new Owner();
    $owner->setFirstname("Alpha");
    $owner->setLastname("Beta");
    $owner->setCountry("FR");
    $manager->persist($owner);
    
    $manager->flush();
    
    $owner = new Owner();
    $owner->setFirstname("Kappa");
    $owner->setLastname("Omikron");
    $owner->setCountry("JP");
    $manager->persist($owner);
    
    $manager->flush();

    $owner = new Owner();
    $owner->setFirstname("Zeta");
    $owner->setLastname("Omega");
    $owner->setCountry("US");
    $manager->persist($owner);
    
    $manager->flush();

    $owner = new Owner();
    $owner->setFirstname("Epsilon");
    $owner->setLastname("Omega");
    $owner->setCountry("KE");
    $manager->persist($owner);
    
    $manager->flush();
    
    $region = new Region();
    $region->setCountry("FR");
    $region->setName("Ile de France");
    $region->setPresentation("La région française capitale");
    $manager->persist($region);

    $manager->flush();
    // Une fois l'instance de Region sauvée en base de données,
    // elle dispose d'un identifiant généré par Doctrine, et peut
    // donc être sauvegardée comme future référence.
    $this->addReference(self::IDF_REGION_REFERENCE, $region);

    $region = new Region();
    $region->setCountry("JP");
    $region->setName("Tokyo");
    $region->setPresentation("La capitale du Japon");
    $manager->persist($region);

    $manager->flush();

    $region = new Region();
    $region->setCountry("US");
    $region->setName("New York");
    $region->setPresentation("La plus grande ville des États-Unis");
    $manager->persist($region);

    $manager->flush();

    $region = new Region();
    $region->setCountry("KE");
    $region->setName("Nairobi");
    $region->setPresentation("La capitale du Kenya");
    $manager->persist($region);

    $manager->flush();

    // ...
    
    $room = new Room();
    $room->setSummary("Beau poulailler ancien à Évry");
    $room->setDescription("très joli espace sur paille");
    $room->setOwner($manager->getRepository(Owner::class)->findOneBy(['firstname' => 'Alpha']));
    $room->addRegion($manager->getRepository(Region::class)->findOneBy(['name' => 'Ile de France']));   
    $manager->persist($room);

    $manager->flush();

    $room = new Room();
    $room->setSummary("Penthouse tokyoïte");
    $room->setDescription("C'est grand et c'est beau");
    $room->setOwner($manager->getRepository(Owner::class)->findOneBy(['firstname' => 'Kappa']));
    $room->addRegion($manager->getRepository(Region::class)->findOneBy(['name' => 'Tokyo']));   
    $manager->persist($room);

    $manager->flush();

    $room = new Room();
    $room->setSummary("Petit appartement New Yorkais");
    $room->setDescription("C'est petit mais c'est beau");
    $room->setOwner($manager->getRepository(Owner::class)->findOneBy(['firstname' => 'Zeta']));
    $room->addRegion($manager->getRepository(Region::class)->findOneBy(['name' => 'New York']));   
    $manager->persist($room);

    $manager->flush();

    //...
    }

    //...
}
