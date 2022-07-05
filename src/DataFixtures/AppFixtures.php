<?php

namespace App\DataFixtures;

use App\Entity\About;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // create about
        //$content = '<p>' . join($faker->paragraphs(5)) . '</p>';
        $about = new About();
        $about
            ->setTitle($faker->sentence())
            ->setContent($faker->paragraphs(5, true))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
        $manager->persist($about);

        // create 10 category
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category();
            $category
                ->setName($faker->sentence())
                ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($category);

            // create between 6 and 10 articles
            for ($j = 1; $j <= mt_rand(6, 10); $j++) {
                $post = new Post();
                $post->setTitle($faker->sentence())
                    ->setSlug($this->slugger->slug($post->getTitle()))
                    ->setSummary($faker->paragraphs(3, true))
                    ->setContent('<p>' . join($faker->paragraphs(20)) . '</p>')
                    ->setCreatedAt($faker->dateTimeBetween('- 6 months'))
                    ->setUpdatedAt($post->getCreatedAt())
                    ->setCategories($category);
                $manager->persist($post);

                // create between 4 and 6 comments
                for ($k = 1; $k <= 6; $k++) {
                    $comment = new Comment();
                    $comment->setName($faker->name)
                        ->setEmail($faker->email)
                        ->setContent('<p>' . join($faker->paragraphs(3)) . '</p>')
                        ->setPublishedAt($faker->dateTimeBetween('-' . (new \DateTime())->diff($post->getCreatedAt())->days . ' days'))
                        ->setPost($post);
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
